<?php

namespace App\Http\Controllers\Attendance;

use App\Exports\Attendance\AttendanceMonthlyExport;
use App\Http\Controllers\Controller;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceShift;
use App\Models\Attendance\AttendanceWorkSchedule;
use App\Models\Employee\Employee;
use App\Models\Setting\AppMasterData;
use Barryvdh\Debugbar\Facades\Debugbar;
use Carbon\Carbon;
use FPDFTable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Str;
use Yajra\DataTables\DataTables;

ini_set('memory_limit', '4096M');

class AttendanceMonthlyController extends Controller
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
    public function index(Request $request)
    {
        $units = AppMasterData::whereAppMasterCategoryCode('EMU')->pluck('name', 'id')->toArray();
        $ranks = AppMasterData::whereAppMasterCategoryCode('EP')->pluck('name', 'id')->toArray();
        $filterMonth = $request->get('filterMonth') ?? date('m');
        $filterYear = $request->get('filterYear') ?? date('Y');
        $user = Auth::user();

        Session::put('user', $user);

        $arr['units'] = $units;
        $arr['ranks'] = $ranks;
        $arr['filterMonth'] = $filterMonth;
        $arr['filterYear'] = $filterYear;
        $arr['totalDays'] = Carbon::create($filterYear, $filterMonth, 1)->daysInMonth;

        return view('attendances.monthly.index', $arr);
    }

    public function data(Request $request, $filterMonth, $filterYear){
        $user = Session::get('user');
        $totalDays = Carbon::create($filterYear, $filterMonth, 1)->daysInMonth;
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            $filterUnit = $request->get('combo_3');
            $filterRank = $request->get('combo_4');
            $employees = DB::table('employees as t1')->join('employee_positions as t2', function ($join){
                $join->on('t1.id', 't2.employee_id');
                $join->where('t2.status', 't');
            });
            if($filterRank) $employees->where('rank_id', $filterRank);
            if($filterUnit) $employees->where('unit_id', $filterUnit);
            if(!$user->hasPermissionTo('lvl3 '.$this->menu_path()))
                $employees->where('t2.leader_id', $user->employee_id);
            $filteredEmployees = clone $employees;
            $filteredEmployees = $filteredEmployees->select('t1.id')->get();

            $arrData = $this->datas($filteredEmployees, $filterMonth, $filterYear);

            $table = DataTables::of($employees)
                ->filter(function ($query) use ($filter) {
                    $query->where(function ($query) use ($filter) {
                        $query->where('name', 'like', "%$filter%")
                            ->orWhere('employee_number', 'like', "%$filter%");
                    });
                });
            for ($i = 1; $i <= $totalDays; $i++) {
                $date = $filterYear.'-'.str_pad($filterMonth, 2, '0', STR_PAD_LEFT).'-' . str_pad($i, 2, '0', STR_PAD_LEFT);

                $table->addColumn('in_day_' . $i, function ($row) use ($i, $arrData, $date) {
                    return $arrData[$row->id][$date]["in"];
                });

                $table->addColumn('pt_day_' . $i, function ($row) use ($i, $arrData, $date) {
                    return $arrData[$row->id][$date]["pt"];
                });

                $table->addColumn('out_day_' . $i, function ($row) use ($i, $arrData, $date) {
                    return $arrData[$row->id][$date]["out"];
                });

                $table->addColumn('pc_day_' . $i, function ($row) use ($i, $arrData, $date) {
                    return $arrData[$row->id][$date]["pc"];
                });

                $table->addColumn('desc_day_' . $i, function ($row) use ($i, $arrData, $date) {
                    return $arrData[$row->id][$date]["ket"];
                });
            }
            return $table->addIndexColumn()
                ->make();
        }
    }

    private function datas($employees, $filterMonth, $filterYear){
        $defaultShift = AttendanceShift::orderBy('id')->first();

        $startDate = Carbon::create($filterYear, $filterMonth, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::create($filterYear, $filterMonth, 1)->endOfMonth()->format('Y-m-d');

        $schedules = AttendanceWorkSchedule::whereBetween('date', [$startDate, $endDate])->select(['employee_id', 'date'])->whereNot('shift_id', '0')->get();
        $attendances = Attendance::whereBetween('start_date', [$startDate, $endDate])->select(['start_time', 'end_time', 'employee_id', 'type', 'start_date'])->get();

        $arrSchedules = [];
        foreach ($schedules as $schedule) {
            $arrSchedules[$schedule->employee_id][$schedule->date] = $schedule;
        }

        $arrAttendances = [];
        foreach ($attendances as $attendance) {
            $arrAttendances[$attendance->employee_id][$attendance->start_date] = $attendance;
        }

        $arrData = [];
        for ($i = 1; $i <= Carbon::create($filterYear, $filterMonth, 1)->daysInMonth; $i++) {
            $date = Carbon::create($filterYear, $filterMonth, $i)->format('Y-m-d');
            foreach ($employees as $employee) {
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
                        $arrData[$employee->id][$date]['type'] = $data->type;
                    }
                }

                $isFuture = false;
                $date = $filterYear . '-' . str_pad($filterMonth, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                $carbonDate = Carbon::create($date);
                $dateNow = Carbon::now()->format('Y-m-d');
                if ($dateNow < $date) $isFuture = true;

                //IN
                $arrData[$employee->id][$date]["in"] = $arrData[$employee->id][$date]["in"] ?? '';
                $data = $arrData[$employee->id][$date]["in"];
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
                                $pt = '0,50';
                            } elseif ($diffInMinutes <= 60){
                                $pt = '1,00';
                            } elseif ($diffInMinutes <= 90){
                                $pt = '1,25';
                            } else {
                                $pt = '1,50';
                            }
                        }
                    }
                }
                if(!$isFuture) $pt = empty($pt) && $carbonDate->isWeekday() ? '0,00' : $pt;
                $arrData[$employee->id][$date]["pt"] = empty($pt) ? '0,00' : $pt;

                //OUT
                $arrData[$employee->id][$date]["out"] = $arrData[$employee->id][$date]["out"] ?? '';
                $data = $arrData[$employee->id][$date]["out"];
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
                                $pc = '0,50';
                            } elseif ($diffInMinutes <= 60){
                                $pc = '1,00';
                            } elseif ($diffInMinutes <= 90){
                                $pc = '1,25';
                            } else {
                                $pc = '1,50';
                            }
                        }
                    }
                }
                if(!$isFuture) $pc = empty($pc) && $carbonDate->isWeekday() ? '0,00' : $pc;
                $arrData[$employee->id][$date]["pc"] = empty($pc) ? '0,00' : $pc;

                //KET
                $data = $arrData[$employee->id][$date]["type"] ?? '';
                $arrData[$employee->id][$date]["ket"] = $data == '2' ? 'WFH' : '';
            }
        }

        return $arrData;
    }

    public function export(Request $request)
    {
        $user = Session::get('user');
        $filterMonth = $request->get('filterMonth');
        $filterYear = $request->get('filterYear');
        $units = AppMasterData::whereAppMasterCategoryCode('EMU');
        if ($request->get('combo_3') && $request->get('combo_3') != 'undefined') $units->whereId($request->get('combo_3'));
        $units = $units->pluck('name', 'id')->toArray();

        $no = 0;
        $data = [];
        $totalDays = Carbon::create($filterYear, $filterMonth, 1)->daysInMonth;
        foreach ($units as $key => $value) {
            $sql = DB::table('employees as t1')->join('employee_positions as t2', function ($join){
                $join->on('t1.id', 't2.employee_id');
                $join->where('t2.status', 't');
            });
            if(!empty($request->get('filter')))
                $sql->where('name', 'like', '%' . $request->get('filter') . '%')
                    ->orWhere('employee_number', 'like', '%' . $request->get('filter') . '%');
            $sql->where('t2.unit_id', $key);
            if($request->get('combo_4') && $request->get('combo_4') != 'undefined') $sql->where('t2.rank_id', $request->get('combo_4'));
            if(!$user->hasPermissionTo('lvl3 '.$this->menu_path())) $sql->where('t2.leader_id', $user->employee_id);
            $employees = $sql->get();

            $arrData = $this->datas($employees, $filterMonth, $filterYear);

            foreach ($employees as $employee) {
                $no++;

                $data[$key][$employee->id] = [
                    'no' => $no,
                    'employee_number' => $employee->employee_number . " ",
                    'name' => $employee->name,
                ];

                for ($i = 1; $i <= $totalDays; $i++) {
                    $date = $filterYear . '-' . str_pad($filterMonth, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                    $data[$key][$employee->id][$i . "_in"] = $arrData[$employee->id][$date]["in"];
                    $data[$key][$employee->id][$i . "_pt"] = $arrData[$employee->id][$date]["pt"];
                    $data[$key][$employee->id][$i . "_out"] = $arrData[$employee->id][$date]["out"];
                    $data[$key][$employee->id][$i . "_pc"] = $arrData[$employee->id][$date]["pc"];
                    $data[$key][$employee->id][$i . "_ket"] = $arrData[$employee->id][$date]["ket"];
                }
            }
        }

        return Excel::download(new AttendanceMonthlyExport(
            [
                'data' => $data,
                'headerTitle' => 'Data Absen Harian',
                'headerSubtitle' => "PERIODE : ".numToMonth($request->get('filterMonth')).' '.$request->get('filterYear'),
                'totalDays' => Carbon::create($request->get('filterYear').'-'.$request->get('filterMonth'))->daysInMonth,
                'units' => $units,
            ]
        ), 'Data Bulanan.xlsx');
    }

    public function pdf(Request $request)
    {
        $user = Session::get('user');
        $filterMonth = $request->get('filterMonth');
        $filterYear = $request->get('filterYear');

        $pdf = new FPDFTable('L', 'mm', ['250', '400']);
        $pdf->SetAutoPageBreak(TRUE);
        $pdf->SetTitle('REKAP ABSEN SEMUA UNIT ' . strtoupper(numToMonth($request->get('filterMonth'))) . ' ' . $request->get('filterYear'));

        $units = AppMasterData::whereAppMasterCategoryCode('EMU');
        if ($request->get('combo_3') && $request->get('combo_3') != 'undefined') $units->whereId($request->get('combo_3'));
        $units = $units->pluck('name', 'id')->toArray();
        foreach ($units as $k => $unit) {
            $employees = Employee::select(['id', 'name'])
                ->whereHas('position', function($query) use ($request, $user, $k){
                    $query->select(['id', 'employee_id', 'position_id']);
                    $query->where('unit_id', $k);
                })
                ->get();

            $arrData = $this->datas($employees, $filterMonth, $filterYear);

            $arrayColumn = array("1" => "1-5", "2" => "6-10", "3" => "11-15", "4" => "16-20", "5" => "21-25", "6" => "26-30");
            foreach ($arrayColumn as $key => $value) {
                $pdf->AddPage();
                /** TITLE START */
                $pdf->Image("assets/media/logos/logo-2.png", $pdf->GetX() + 90, $pdf->GetY() + 1, 20, 18);

                $pdf->SetTextColor(0, 153, 0);
                $pdf->SetXY(1, 10);
                $pdf->SetFont('Arial', 'B', 20);
                $pdf->Cell(400, 5, 'KEMENTERIAN AGAMA', 0, 0, 'C');
                $pdf->SetFont('Arial', 'B', 13);
                $pdf->SetXY(1, 18);
                $pdf->Cell(400, 5, 'KANTOR WILAYAH PROVINSI DAERAH KHUSUS IBUKOTA JAKARTA', 0, 0, 'C');
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->SetXY(1, 24);
                $pdf->Cell(400, 5, $unit, 0, 0, 'C');
                $pdf->SetFont('Arial', '', 11);
                $pdf->SetXY(1, 30);
                $pdf->Cell(400, 5, 'BULAN LAPORAN : '.Str::upper(numToMonth($filterMonth)).' '.$filterYear, 0, 0, 'C');
                $pdf->Ln(8);
                /** TITLE END */

                /** HEADER START */
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(10, 10, 'No.', 1, 0, 'C');
                $pdf->Cell(55, 10, 'Nama Pegawai', 1, 0, 'C');
                $pdf->Cell(55, 10, 'Jabatan', 1, 0, 'C');
                $arrText = [];
                $arrWidthHeader = [];
                $arrAlignsHeader = [];
                list($firstColumn, $lastColumn) = explode("-", $value);
                $arrWidth = [];
                $arrAligns = [];
                $arrTextSub = [];
                for ($i = $firstColumn; $i <= $lastColumn; $i++) {
                    $arrWidthHeader[] = 50;
                    $arrAlignsHeader[] = 'C';
                    $arrText[] = (string)$i;
                    $arrTextSub = [];
                    $arrWidth = [];
                    $arrAligns = [];
                    for ($j = 1; $j <= 5; $j++) {
                        $arrWidth[] = 10;
                        $arrWidth[] = 10;
                        $arrWidth[] = 10;
                        $arrWidth[] = 10;
                        $arrWidth[] = 10;
                        $arrAligns[] = 'C';
                        $arrAligns[] = 'C';
                        $arrAligns[] = 'C';
                        $arrAligns[] = 'C';
                        $arrAligns[] = 'C';
                        $arrTextSub[] = "MSK\t";
                        $arrTextSub[] = "PT\t";
                        $arrTextSub[] = "PLG\t";
                        $arrTextSub[] = "PC\t";
                        $arrTextSub[] = "KET\t";
                    }
                }

                $pdf->setWidths($arrWidthHeader);
                $pdf->setAligns($arrAlignsHeader);
                $pdf->Row($arrText);
                $pdf->Cell(120, 0, '', 0, 0, 'C');
                $pdf->SetWidths($arrWidth);
                $pdf->SetAligns($arrAligns);
                $pdf->Row($arrTextSub);
                /* HEADER END */

                /** DATA START */
                $arrHeader = array("10", "55", "55");
                $arrAlign = array("C", "L", "L");
                $arrHeader = array_merge($arrHeader, $arrWidth);
                $arrAlign = array_merge($arrAlign, $arrAligns);
                $pdf->setWidths($arrHeader);
                $pdf->setAligns($arrAlign);

//                dd($arrData);

                $pdf->SetFont('Arial', '', 7);
                $no = 0;
                foreach ($employees as $employee) {
                    $no++;
                    $positionName = $employee->position->position_id ? AppMasterData::find($employee->position->position_id)->name : '';
                    $arrValue = [];
                    for ($i = $firstColumn; $i <= $lastColumn; $i++) {
                        $date = $filterYear . '-' . str_pad($filterMonth, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                        $arrValue[] = $arrData[$employee->id][$date]["in"] . "\t";
                        $arrValue[] = $arrData[$employee->id][$date]["pt"] . "\t";
                        $arrValue[] = $arrData[$employee->id][$date]["out"] . "\t";
                        $arrValue[] = $arrData[$employee->id][$date]["pc"] . "\t";
                        $arrValue[] = $arrData[$employee->id][$date]["ket"] . "\t";
                    }
                    $arrEmployee = array(
                        $no . "\t",
                        $employee->name . "\t",
                        $positionName . "\t",
                    );
                    $arrEmployee = array_merge($arrEmployee, $arrValue);
                    $pdf->Row($arrEmployee);
                }

                $pdf->SetXY(10, 240);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(7, 0, 'MSK', 0, 0, 'L');
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(29, 0, ': Jam absensi masuk | ', 0, 0, 'L');
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(7, 0, 'PLG', 0, 0, 'L');
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(30, 0, ': Jam absensi pulang | ', 0, 0, 'L');
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(5, 0, 'PT', 0, 0, 'L');
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(31, 0, ': Potongan jam masuk | ', 0, 0, 'L');
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(5, 0, 'PC', 0, 0, 'L');
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(30, 0, ': Potongan jam pulang', 0, 0, 'L');
            }
        }

        $pdf->Output();
        exit;
    }
}
