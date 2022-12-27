<?php

namespace App\Http\Controllers\Payroll;

use App\Exports\Payroll\PayrollRecapExport;
use App\Http\Controllers\Controller;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceShift;
use App\Models\Attendance\AttendanceWorkSchedule;
use App\Models\Employee\Employee;
use App\Models\Payroll\PayrollSetting;
use App\Models\Setting\AppMasterData;
use Carbon\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Str;
use Yajra\DataTables\DataTables;

class PayrollRecapController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $units = AppMasterData::whereAppMasterCategoryCode('EMU')->pluck('name', 'id')->toArray();
        $ranks = AppMasterData::whereAppMasterCategoryCode('EP')->pluck('name', 'id')->toArray();
        $user = Auth::user();

        \Session::put('user', $user);

        return view('payrolls.payroll-recap.index', [
            'units' => $units,
            'ranks' => $ranks,
        ]);
    }

    public function data(Request $request)
    {
        $masters = AppMasterData::whereIn('app_master_category_code', ['EG', 'EP', 'ETP'])->pluck('name', 'id')->toArray();
        $user = \Session::get('user');

        if($request->ajax()){
            $filter = $request->get('search')['value'];
            $filterUnit = $request->get('combo_3');
            $filterRank = $request->get('combo_4');
            $filterMonth = $request->get('filter_1') ?? date('m');
            $filterYear = $request->get('filter_2') ?? date('Y');

            $employees = DB::table('employees as t1')
                ->join('employee_positions as t2', function ($join) {
                    $join->on('t1.id', 't2.employee_id')
                        ->where('t2.status', 't');
                })
                ->select(['t1.id', 't1.name', 't1.employee_number', 't2.rank_id', 't2.grade_id', 't2.employee_type_id', 't2.unit_id']);
            if(!$user->hasPermissionTo('lvl3 '.$this->menu_path())) $employees->where('t2.leader_id', $user->employee_id);
            $arrDataAttendances = $this->attendances($employees->get(), $filterMonth, $filterYear);

            return DataTables::of($employees)
                ->filter(function ($query) use ($filter, $filterRank, $filterUnit) {
                    if($filterUnit) $query->where('t2.unit_id', $filterUnit);
                    if($filterRank) $query->where('t2.rank_id', $filterRank);
                    $query->where(function ($query) use ($filter) {
                        $query->where('t1.name', 'like', "%$filter%")
                            ->orWhere('t1.employee_number', 'like', "%$filter%");
                    });

                })
                ->editColumn('grade_id', function ($model) use ($masters) {
                    return $model->grade_id ? $masters[$model->grade_id] : '';
                })
                ->editColumn('rank_id', function ($model) use ($masters) {
                    return $model->rank_id ? $masters[$model->rank_id] : '';
                })
                ->editColumn('employee_type_id', function ($model) use ($masters) {
                    return $model->employee_type_id ? $masters[$model->employee_type_id] : '';
                })
                ->addColumn('rankAmount', function ($model) use ($arrDataAttendances) {
                    return setCurrency($arrDataAttendances[$model->id]['rankAmount']);
                })
                ->addColumn('null', function ($data) {
                    return 0;
                })
                ->addColumn('pt', function ($data) use ($arrDataAttendances) {
                    return $arrDataAttendances[$data->id]['pt'];
                })
                ->addColumn('pc', function ($data) use ($arrDataAttendances) {
                    return $arrDataAttendances[$data->id]['pc'];
                })
                ->addColumn('tptc', function ($data) use ($arrDataAttendances) {
                    return $arrDataAttendances[$data->id]['tptc'];
                })
                ->addColumn('percent', function ($data) use ($arrDataAttendances) {
                    return $arrDataAttendances[$data->id]['percent'];
                })
                ->addColumn('deduction', function ($data) use ($arrDataAttendances) {
                    return setCurrency($arrDataAttendances[$data->id]['deduction']);
                })
                ->addColumn('total', function ($data) use ($arrDataAttendances) {
                    return setCurrency($arrDataAttendances[$data->id]['total']);
                })
                ->addIndexColumn()
                ->make();
        }
    }

    public function export(Request $request)
    {
        $masters = AppMasterData::whereIn('app_master_category_code', ['EG', 'EP', 'ETP', 'EMU'])->pluck('name', 'id')->toArray();
        $arrRankAmount = PayrollSetting::where('code', 'TJB')->pluck('amount', 'reference_id')->toArray();

        $units = AppMasterData::whereAppMasterCategoryCode('EMU');
        if ($request->get('combo_3') && $request->get('combo_3') != 'undefined') $units->whereId($request->get('combo_3'));
        $units = $units->pluck('name', 'id')->toArray();

        $data = [];
        foreach ($units as $key => $value) {
            $sql = DB::table('employees as t1')
                ->join('employee_positions as t2', function ($join) {
                    $join->on('t1.id', 't2.employee_id')
                        ->where('t2.status', 't');
                });
            if (!empty($request->get('filter')))
                $sql->where('name', 'like', '%' . $request->get('filter') . '%')
                    ->orWhere('employee_number', 'like', '%' . $request->get('filter') . '%');
            $sql->where('t2.unit_id', $key);
            if ($request->get('combo_4')) $sql->where('t2.rank_id', $request->get('combo_4'));

            $employees = $sql->select(['t1.id', 't1.name', 't1.employee_number', 't2.rank_id', 't2.grade_id', 't2.employee_type_id', 't2.unit_id'])->get();
            $no = 0;
            foreach ($employees as $employee) {
                $no++;
                $rankAmount = $arrRankAmount[$employee->rank_id] ?? 0;
                $data[$key][$employee->id] = [
                    $no,
                    $employee->name,
                    $employee->employee_number . " ",
                    $employee->grade_id ? $masters[$employee->grade_id] : '',
                    $employee->rank_id ? $masters[$employee->rank_id] : '',
                    setCurrency($rankAmount),
                    $employee->employee_type_id ? $masters[$employee->employee_type_id] : '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    setCurrency($rankAmount),
                ];
            }
        }

        return Excel::download(new PayrollRecapExport(
            [
                'data' => $data,
                'headerTitle' => 'Rekap Tukin',
                'headerSubtitle' => "PERIODE ".strtoupper(numToMonth($request->get('filter_1')))." ".$request->get('filter_2'),
                'additional_title' => $request->get('combo_3') ? 'UNIT : '.$masters[$request->get('combo_3')] : 'SEMUA UNIT',
                'units' => $units,
            ]
        ), 'payroll-recap.xlsx');
    }

    public function pdf(Request $request)
    {
        $user = \Session::get('user');
        $employee = Employee::find($user->employee_id);

        $unitName = 'SEMUA UNIT';
        if(!$user->hasPermissionTo('lvl3 '.$this->menu_path())){
            $unitName = AppMasterData::find($employee->position->unit_id)->name;
        }else{
            if($request->get('combo_3')) $unitName = AppMasterData::find($request->get('combo_3'))->name;
        }

        $pdf = new Fpdf('L', 'mm', ['250', '410']);
        $pdf->SetTitle('REKAP TUKIN '.$unitName.' ' . strtoupper(numToMonth($request->get('filter_1'))) . ' ' . $request->get('filter_2'));

        $units = AppMasterData::whereAppMasterCategoryCode('EMU');
        if($request->get('combo_3') && $request->get('combo_3') != 'undefined') $units->whereId($request->get('combo_3'));
        $units = $units->get();

        foreach ($units as $unit){
            $datas = $this->employees($request, $unit->id);

            $pdf->AddPage();

            /** TITLE START */
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(200, 7, 'DAFTAR TUNJANGAN KINERJA PEGAWAI', 0, 0, 'L');
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(25, 7, 'UNIT KERJA', 0, 0, 'L');
            $pdf->Cell(3, 7, ':', 0, 0, 'L');
            $pdf->Cell(30, 7, $unit->name, 0, 0, 'L');
            $pdf->Ln(5);
            $pdf->Cell(25, 7, 'BULAN', 0, 0, 'L');
            $pdf->Cell(3, 7, ':', 0, 0, 'L');
            $pdf->Cell(30, 7, numToMonth($request->get('filter_1'))." ".$request->get('filter_2'), 0, 0, 'L');
            $pdf->Ln(8);
            /** TITLE END */

            /** HEADER START */
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(10,15,'No.', 1,0, 'C');
            $pdf->Cell(60,15,'Nama', 1,0, 'C');
            $pdf->Cell(35,15,'NIP', 1,0, 'C');
            $pdf->Cell(15,15,'Gol', 1,0, 'C');
            $pdf->Cell( 15, 15, '', 1);
            $pdf->Cell( 20, 15, '', 1);
            $pdf->Cell(15,15,'Status', 1,0, 'C');
            $pdf->Cell(15,15,'PT', 1,0, 'C');
            $pdf->Cell(15,15,'PC', 1,0, 'C');
            $pdf->Cell(15,15,'TPTC', 1,0, 'C');
            $pdf->Cell(45,7.5,'Hukuman Disiplin', 1,0, 'C');
            $pdf->Cell( 25, 15, '', 1);
            $pdf->Cell( 30, 15, '', 1);
            $pdf->Cell( 15, 15, '', 1);
            $pdf->Cell(35,7.5,'Total Pemotongan', 1,0, 'C');
            $pdf->Cell(20,15,'', 1);

            $pdf->Ln(-2);
            $pdf->Cell(120, 8);
            $pdf->Cell( 15, 15, 'Kelas', '', '', 'C');
            $pdf->Cell( 20, 15, 'Nilai', '', '', 'C');
            $pdf->Cell(160, 15);
            $pdf->Cell( 15, 15, 'Izin', '', '', 'C');
            $pdf->Cell(37, 15);
            $pdf->Cell( 15, 15, 'Jml.', '', '', 'C');
            $pdf->Ln(4);
            $pdf->Cell(120, 8);
            $pdf->Cell( 15, 15, 'Jabatan', '', '', 'C');
            $pdf->Cell( 20, 15, 'Kelas', '', '', 'C');
            $pdf->Cell(160, 15);
            $pdf->Cell( 15, 15, 'Belajar', '', '', 'C');
            $pdf->Cell(37, 15);
            $pdf->Cell( 15, 15, 'Diterima', '', '', 'C');
            $pdf->Ln(5.5);
            $pdf->Cell(215,15);
            $pdf->Cell(15,7.5,'Ringan', 1,0, 'C');
            $pdf->Cell(15,7.5,'Sedang', 1,0, 'C');
            $pdf->Cell(15,7.5,'Berat', 1,0, 'C');
            $pdf->Cell(70,15);
            $pdf->Cell(15,7.5,'%', 1,0, 'C');
            $pdf->Cell(20,7.5,'(Rp)', 1,0, 'C');
            $pdf->Ln(-11);
            $pdf->Cell(265,15);
            $pdf->Cell( 15, 15, 'Sakit > 3 Hari', '', '', 'C');
            $pdf->Ln(4);
            $pdf->Cell(265,15);
            $pdf->Cell( 15, 15, 'Tanpa', '', '', 'C');
            $pdf->Ln(4);
            $pdf->Cell(265,15);
            $pdf->Cell( 15, 15, 'Keterangan', '', '', 'C');

            $pdf->Ln(-8);
            $pdf->Cell(292.5,15);
            $pdf->Cell( 15, 15, 'Cuti Besar /', '', '', 'C');
            $pdf->Ln(4);
            $pdf->Cell(292.5,15);
            $pdf->Cell( 15, 15, 'Diluar Tanggungan', '', '', 'C');
            $pdf->Ln(4);
            $pdf->Cell(292.5,15);
            $pdf->Cell( 15, 15, 'Negara', '', '', 'C');
            /** HEADER END */

            /** DATA START */
            $pdf->Ln(10.5);
            $pdf->SetFont('Arial', '', 7);
            $no = 0;
            foreach ($datas as $data){
                $no++;
                $pdf->Cell(10,5, $no, 1,0, 'C');
                $pdf->Cell(60,5, $data['name'], 1);
                $pdf->Cell(35,5, $data['employee_number'], 1, 0, 'C');
                $pdf->Cell(15,5, $data['grade_id'], 1,0, 'C');
                $pdf->Cell(15,5, $data['rank_id'], 1,0, 'C');
                $pdf->Cell(20,5, $data['rank_amount'], 1,0, 'R');
                $pdf->Cell(15,5, $data['employee_type_id'], 1,0, 'C');
                $pdf->Cell(15,5, $data['pt'], 1,0, 'R');
                $pdf->Cell(15,5, $data['pc'], 1,0, 'R');
                $pdf->Cell(15,5, $data['tptc'], 1,0, 'R');
                $pdf->Cell(15,5, $data['hk_easy'], 1,0, 'R');
                $pdf->Cell(15,5, $data['hk_medium'], 1,0, 'R');
                $pdf->Cell(15,5, $data['hk_hard'], 1,0, 'R');
                $pdf->Cell(25,5, $data['sick'], 1,0, 'R');
                $pdf->Cell(30,5, $data['leave'], 1,0, 'R');
                $pdf->Cell(15,5, $data['study'], 1,0, 'R');
                $pdf->Cell(15,5, $data['percent'], 1,0, 'R');
                $pdf->Cell(20,5, $data['deduction'], 1,0, 'R');
                $pdf->Cell(20,5, $data['total'], 1,0, 'R');
                $pdf->Ln();
            }
            /** DATA END */

            /** FOOTER START */
            $currentY = $pdf->GetY();
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetXY(325, $currentY + 10);
            $pdf->Cell(40,0, 'Jakarta, 16 Juli 2022', '','','C');
            $pdf->SetXY(325, $currentY + 15);
            $pdf->Cell(40,0, 'Kepala Bagian Tata Usaha', '','','C');

            $pdf->SetFont('Arial', 'BU', 9);
            $pdf->SetXY(325, $currentY + 35);
            $pdf->Cell(40,0, '    H. SUGITO, S.Ag, M.Pd.I    ', '','','C');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetXY(325, $currentY + 40);
            $pdf->Cell(40,0, 'NIP : 197006182002121002', '','','C');

            /**  FOOTER END */
        }

        $pdf->Output();
        exit;
    }

    public function employees(Request $request, $unit)
    {
        $masters = AppMasterData::whereIn('app_master_category_code', ['EG', 'EP', 'ETP', 'EMU'])->pluck('name', 'id')->toArray();
        $arrRankAmount = PayrollSetting::where('code', 'TJB')->pluck('amount', 'reference_id')->toArray();
        $user = \Session::get('user');

        $sql = DB::table('employees as t1')
            ->join('employee_positions as t2', function ($join) {
                $join->on('t1.id', 't2.employee_id')
                    ->where('t2.status', 't');
            });
        if($unit) $sql->where('t2.unit_id', $unit);
        if(!$user->hasPermissionTo('lvl3 '.$this->menu_path())) $sql->where('t2.leader_id', $user->employee_id);

        if (!empty($request->get('filter')))
            $sql->where('name', 'like', '%' . $request->get('filter') . '%')
                ->orWhere('employee_number', 'like', '%' . $request->get('filter') . '%');
        if ($request->get('combo_4') != 'undefined' && $request->get('combo_4') != null && $request->get('combo_4') != '') $sql->where('t2.rank_id', $request->get('combo_4'));

        $employees = $sql->select(['t1.id', 't1.name', 't1.employee_number', 't2.rank_id', 't2.grade_id', 't2.employee_type_id', 't2.unit_id'])->orderBy('name')->get();
        $arrDataAttendances = $this->attendances($employees, $request->get('filter_1'), $request->get('filter_2'));
        $data = [];
        $no = 0;
        foreach ($employees as $employee) {
            $no++;
            $rankAmount = $arrRankAmount[$employee->rank_id] ?? 0;
            $grade_id = $employee->grade_id ? $masters[$employee->grade_id] : '';
            $rank_id = $employee->rank_id ? $masters[$employee->rank_id] : '';
            $data[] = [
                'no' => $no,
                'name' =>$employee->name,
                'employee_number' =>$employee->employee_number . " ",
                'grade_id' => $grade_id,
                'rank_id' => Str::replace('Kelas Jabatan', '', $rank_id),
                'rank_amount' => setCurrency($rankAmount),
                'employee_type_id' => $employee->employee_type_id ? $masters[$employee->employee_type_id] : '',
                'pt' => $arrDataAttendances[$employee->id]['pt'],
                'pc' => $arrDataAttendances[$employee->id]['pc'],
                'tptc' => $arrDataAttendances[$employee->id]['tptc'],
                'hk_easy' => $arrDataAttendances[$employee->id]['hk_easy'],
                'hk_medium' => $arrDataAttendances[$employee->id]['hk_medium'],
                'hk_hard' => $arrDataAttendances[$employee->id]['hk_hard'],
                'sick' => $arrDataAttendances[$employee->id]['sick'],
                'leave' => $arrDataAttendances[$employee->id]['leave'],
                'study' => $arrDataAttendances[$employee->id]['study'],
                'percent' => $arrDataAttendances[$employee->id]['percent'] ?? 0,
                'deduction' => setCurrency($arrDataAttendances[$employee->id]['deduction'] ?? 0),
                'total' => setCurrency($arrDataAttendances[$employee->id]['total'] ?? 0),
            ];
        }

        return $data;
    }

    public function attendances($employees, $filterMonth, $filterYear){
        $defaultShift = AttendanceShift::orderBy('id')->first();

        $startDate = Carbon::create($filterYear, $filterMonth, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::create($filterYear, $filterMonth, 1)->endOfMonth()->format('Y-m-d');

        $schedules = AttendanceWorkSchedule::whereBetween('date', [$startDate, $endDate])->select(['employee_id', 'date'])->whereNot('shift_id', '0')->get();
        $attendances = Attendance::whereBetween('start_date', [$startDate, $endDate])->select(['start_time', 'end_time', 'employee_id', 'type', 'start_date'])->get();

        $arrRankAmount = PayrollSetting::where('code', 'TJB')->pluck('amount', 'reference_id')->toArray();

        $arrSchedules = [];
        foreach ($schedules as $schedule) {
            $arrSchedules[$schedule->employee_id][$schedule->date] = $schedule;
        }

        $arrAttendances = [];
        foreach ($attendances as $attendance) {
            $arrAttendances[$attendance->employee_id][$attendance->start_date] = $attendance;
        }

        $arrData = [];
        $arrTotal = [];
        foreach ($employees as $employee) {
            $arrTotal[$employee->id]["rankAmount"] = $arrRankAmount[$employee->rank_id] ?? 0;
            $arrTotal[$employee->id]["pt"] = 0;
            $arrTotal[$employee->id]["pc"] = 0;
            $arrTotal[$employee->id]["tptc"] = 0;
            $arrTotal[$employee->id]["hk_easy"] = 0;
            $arrTotal[$employee->id]["hk_medium"] = 0;
            $arrTotal[$employee->id]["hk_hard"] = 0;
            $arrTotal[$employee->id]["sick"] = 0;
            $arrTotal[$employee->id]["leave"] = 0;
            $arrTotal[$employee->id]["study"] = 0;
            $arrTotal[$employee->id]["percent"] = 0;
            $arrTotal[$employee->id]["deduction"] = 0;
            $arrTotal[$employee->id]["total"] = 0;
            for ($i = 1; $i <= Carbon::create($filterYear, $filterMonth, 1)->daysInMonth; $i++) {
            $date = Carbon::create($filterYear, $filterMonth, $i)->format('Y-m-d');
                if (isset($arrSchedules[$employee->id][$date]) && !isset($arrAttendances[$employee->id][$date])) {
                    $carbonDate = Carbon::create($date);
                    $dateNow = Carbon::now()->format('Y-m-d');

                    $arrData[$employee->id][$date]['in'] = $carbonDate < $dateNow ? "A" : '';
                    $arrData[$employee->id][$date]['out'] = $carbonDate < $dateNow ? "A" : '';
                } else {
                    $data = $arrAttendances[$employee->id][$date] ?? null;
                    if ($data) {
                        if ($data->type == 'C') {
                            $arrData[$employee->id][$date]['in'] = 'C';
                            $arrData[$employee->id][$date]['out'] = 'C';
                        } else if ($data->type == 'I') {
                            $arrData[$employee->id][$date]['in'] = 'I';
                            $arrData[$employee->id][$date]['out'] = 'I';
                        } else if ($data->type == '3') {
                            $arrData[$employee->id][$date]['in'] = 'DL';
                            $arrData[$employee->id][$date]['out'] = 'DL';
                        } else if ($data->type == '4') {
                            $arrData[$employee->id][$date]['in'] = 'DL';
                            $arrData[$employee->id][$date]['out'] = $data->end_time;
                        } else if ($data->type == '5') {
                            $arrData[$employee->id][$date]['in'] = $data->start_time;
                            $arrData[$employee->id][$date]['out'] = 'DL';
                        } else {
                            $arrData[$employee->id][$date]['in'] = $data->start_time;
                            $arrData[$employee->id][$date]['out'] = $data->end_time;
                        }
                    }
                }

                $isFuture = false;
                $date = $filterYear . '-' . $filterMonth . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                $carbonDate = Carbon::create($date);
                $dateNow = Carbon::now()->format('Y-m-d');
                if ($dateNow < $date) $isFuture = true;

                //IN
                $data = $arrData[$employee->id][$date]["in"] ?? '';
                if(!$isFuture) $data = empty($data) && $carbonDate->isWeekday() ? 'A' : $data;
                $arrData[$employee->id][$date]["in"] = substr($data, 0, 5);

                //PT
                $startShift = $defaultShift->start;
                if (isset($arrSchedule[$employee->id][$date])) $startShift = $arrSchedule[$employee->id][$date]->start_time;
                $pt = '';
                if(isset($arrData[$employee->id][$date]["in"]) && Str::contains($arrData[$employee->id][$date]["in"], ':')){
                    if ($arrData[$employee->id][$date]["in"] > $startShift) {
                        $diffInMinutes = Carbon::parse($arrData[$employee->id][$date]["in"])->diffInMinutes($startShift);

                        if($diffInMinutes) {
                            if ($diffInMinutes <= 30){
                                $pt = '0.50';
                            } elseif ($diffInMinutes <= 60){
                                $pt = '1.00';
                            } elseif ($diffInMinutes <= 90){
                                $pt = '1.25';
                            } else {
                                $pt = '1.50';
                            }
                        }
                    }
                }
                if(!$isFuture) $pt = empty($pt) && $carbonDate->isWeekday() ? '0,00' : $pt;
                $arrTotal[$employee->id]["pt"] += (double)$pt;

                //OUT
                $data = $arrData[$employee->id][$date]["out"] ?? '';
                if(!$isFuture) $data = empty($data) && $carbonDate->isWeekday() ? 'A' : $data;
                $arrData[$employee->id][$date]["out"] = substr($data, 0, 5);

                //PC
                $endShift = $defaultShift->end;
                if (isset($arrSchedule[$employee->id][$date])) $endShift = $arrSchedule[$employee->id][$date]->end_time;
                $pc = '';
                if(isset($arrData[$employee->id][$date]["out"]) && Str::contains($arrData[$employee->id][$date]["out"], ':')){
                    if ($arrData[$employee->id][$date]["out"] < $endShift) {
                        $diffInMinutes = Carbon::parse($arrData[$employee->id][$date]["out"])->diffInMinutes($endShift);

                        if($diffInMinutes) {
                            if ($diffInMinutes <= 30){
                                $pc = '0.50';
                            } elseif ($diffInMinutes <= 60){
                                $pc = '1.00';
                            } elseif ($diffInMinutes <= 90){
                                $pc = '1.25';
                            } else {
                                $pc = '1.50';
                            }
                        }
                    }
                }

                if(!$isFuture) $pc = empty($pc) && $carbonDate->isWeekday() ? '0,00' : $pc;
                $arrTotal[$employee->id]["pc"] += (double)$pc;
                $arrTotal[$employee->id]["tptc"] = $arrTotal[$employee->id]["pt"] + $arrTotal[$employee->id]["pc"];
                $arrTotal[$employee->id]["percent"] = $arrTotal[$employee->id]["tptc"];
                $arrTotal[$employee->id]["deduction"] = $arrTotal[$employee->id]["rankAmount"] * $arrTotal[$employee->id]["percent"] / 100;
                $arrTotal[$employee->id]["total"] = $arrTotal[$employee->id]["rankAmount"] - $arrTotal[$employee->id]["deduction"];
            }
        }

        return $arrTotal;
    }
}
