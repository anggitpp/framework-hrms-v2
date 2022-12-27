<?php

namespace App\Http\Controllers\Attendance;

use App\Exports\Attendance\AttendanceTimesheetRecapExport;
use App\Http\Controllers\Controller;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeSignatureSetting;
use App\Models\ESS\EssTimesheet;
use App\Models\Setting\AppMasterData;
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

class AttendanceTimesheetRecapController extends Controller
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
        $filterMonth = $request->get('filterMonth') ?? date('m');
        $filterYear = $request->get('filterYear') ?? date('Y');
        $user = Auth::user();

        $units = AppMasterData::whereAppMasterCategoryCode('EMU')->pluck('name', 'id')->toArray();
        $ranks = AppMasterData::whereAppMasterCategoryCode('EP')->pluck('name', 'id')->toArray();

        $data['filterMonth'] = $filterMonth;
        $data['filterYear'] = $filterYear;
        $data['units'] = $units;
        $data['ranks'] = $ranks;
        $data['totalDays'] = Carbon::create($filterYear, $filterMonth, 1)->daysInMonth;

        \Session::put('user', $user);

        return view('attendances.timesheet-recap.index', $data);
    }

    public function data(Request $request, $filterMonth, $filterYear){

        $user = \Session::get('user');

        if($request->ajax()){
            $filter = $request->get('search')['value'];
            $filterUnit = $request->get('combo_3');
            $filterRank = $request->get('combo_4');
            $totalDays = Carbon::create($filterYear, $filterMonth, 1)->daysInMonth;

            $employees = DB::table('employees as t1')->join('employee_positions as t2', function ($join){
                $join->on('t1.id', 't2.employee_id');
                $join->where('t2.status', 't');
            });
            if($filterRank) $employees->where('rank_id', $filterRank);
            if($filterUnit) $employees->where('unit_id', $filterUnit);
            if(!$user->hasPermissionTo('lvl3 '.$this->menu_path())) $employees->where('t2.leader_id', $user->employee_id);

            $arrData = $this->datas($filterMonth, $filterYear);

            $table = DataTables::of($employees)
                ->filter(function ($query) use ($filter) {
                    $query->where(function ($query) use ($filter) {
                        $query->where('name', 'like', "%$filter%")
                            ->orWhere('employee_number', 'like', "%$filter%");
                    });

                });
            for ($i = 1; $i <= $totalDays; $i++) {
                $table->addColumn('day_' . $i, function ($row) use ($i, $arrData, $filterMonth, $filterYear) {
                    $date = $filterYear.'-'.Str::padLeft($filterMonth, 2, '0').'-' . Str::padLeft($i, 2, '0');
                    $data = $arrData["timesheet"][$row->id][$date] ?? '';

                    return view('components.datatables.on-click-modal', [
                        'route' => route('attendances.timesheet-recap.detail', [$row->id, $date]),
                        'text' => Str::substr($data, 0, 5),
                        'size' => 'mw-900px'
                    ]);
                });
            }
            $table->addColumn('total', function ($row) use ($arrData) {
                return $arrData["totalTimesheet"][$row->id] ?? '';
            });

            return  $table->addIndexColumn()
                ->make();
        }
    }

    public function detail(int $id, String $date){
        $data['timesheets'] = EssTimesheet::whereEmployeeId( $id)
            ->where('date', $date)
            ->get();

        return view('attendances.timesheet-recap.detail', $data);
    }

    public function export(Request $request)
    {
        $user = \Session::get('user');
        $filterMonth = $request->get('filterMonth');
        $filterYear = $request->get('filterYear');
        $totalDays = Carbon::create($filterYear, $filterMonth, 1)->daysInMonth;

        $arrData = $this->datas($filterMonth, $filterYear);

        $units = AppMasterData::whereAppMasterCategoryCode('EMU');
        if ($request->get('combo_3') && $request->get('combo_3') != 'undefined') $units->whereId($request->get('combo_3'));
        $units = $units->pluck('name', 'id')->toArray();

        $data = [];
        foreach ($units as $key => $value) {

            $sql = DB::table('employees as t1')->join('employee_positions as t2', function ($join) {
                $join->on('t1.id', 't2.employee_id');
                $join->where('t2.status', 't');
            });
            if (!empty($request->get('filter')))
                $sql->where('name', 'like', '%' . $request->get('filter') . '%')
                    ->orWhere('employee_number', 'like', '%' . $request->get('filter') . '%');
            $sql->where('t2.unit_id', $key);
            if ($request->get('combo_4') && $request->get('combo_4') != 'undefined') $sql->where('t2.rank_id', $request->get('combo_4'));
            if (!$user->hasPermissionTo('lvl3 ' . $this->menu_path())) $sql->where('t2.leader_id', $user->employee_id);
            $employees = $sql->get();

            $no = 0;
            foreach ($employees as $employee) {
                $no++;

                $data[$key][$employee->id] = [
                    'no' => $no,
                    'employee_number' => $employee->employee_number . " ",
                    'name' => $employee->name,
                ];

                for ($i = 1; $i <= $totalDays; $i++) {
                    $date = $filterYear . '-' . $filterMonth . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                    $data[$key][$employee->id][(string)$i] =
                        Str::substr($arrData["timesheet"][$employee->id][$date] ?? '', 0, 5);
                }
                $data[$key][$employee->id]['total'] = $arrData["totalTimesheet"][$employee->id] ?? '';
            }
        }

        return Excel::download(new AttendanceTimesheetRecapExport(
            [
                'data' => $data,
                'headerTitle' => 'Data Rekap Kienrja',
                'headerSubtitle' => "PERIODE : ".numToMonth($filterMonth).' '.$filterYear,
                'totalDays' => Carbon::create($filterYear.'-'.$filterMonth)->daysInMonth,
                'units' => $units,
            ]
        ), 'Data Rekap Kinerja.xlsx');
    }

    public function pdf(Request $request)
    {
        $filterMonth = $request->get('filterMonth');
        $filterYear = $request->get('filterYear');
        $totalDays = Carbon::create($filterYear, $filterMonth, 1)->daysInMonth;

        $pdf = new FPDFTable('L', 'mm', ['250', '400']);
        $pdf->SetAutoPageBreak(TRUE);
        $pdf->SetTitle('REKAP KINERJA SEMUA UNIT ' . strtoupper(numToMonth($request->get('filterMonth'))) . ' ' . $request->get('filterYear'));

        $units = AppMasterData::whereAppMasterCategoryCode('EMU');
        //FILTER IF ANY UNIT
        if($request->get('combo_3') && $request->get('combo_3') != 'undefined') $units->whereId($request->get('combo_3'));
        $filterRank = $request->get('combo_4');

        $arrData = $this->datas($filterMonth, $filterYear);

        $units = $units->get();
        foreach ($units as $unit) {
            $employees = Employee::select(['id', 'name', 'employee_number'])
                ->whereHas('position', function($query) use ($filterRank, $user, $unit){
                    $query->select(['id', 'employee_id', 'position_id']);
                    $query->where('unit_id', $unit->id);
                    if($filterRank && $filterRank != 'undefined') $query->where('rank_id', $filterRank);
                });
            $employees = $employees->orderBy('name')->get();

            $pdf->AddPage();
            /** TITLE START */
            $pdf->Image("assets/media/logos/logo-2.png", $pdf->GetX() + 90, $pdf->GetY() + 1, 20, 15);

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
            $pdf->Cell(400, 5, $unit->name, 0, 0, 'C');
            $pdf->SetFont('Arial', '', 11);
            $pdf->SetXY(1, 30);
            $pdf->Cell(400, 5, 'BULAN LAPORAN : '.Str::upper(numToMonth($filterMonth)).' '.$filterYear, 0, 0, 'C');
            $pdf->Ln(8);
            /** TITLE END */

            /** HEADER START */
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(10, 10, 'No.', 1, 0, 'C');
            $pdf->Cell(30, 10, 'NIP', 1, 0, 'C');
            $pdf->Cell(55, 10, 'Nama Pegawai', 1, 0, 'C');
            $pdf->Cell($totalDays * 9, 5, 'Tanggal', 1, 0, 'C');
            $pdf->Ln();
            $pdf->Cell(95);
            $arrWidth = [];
            $arrAligns = [];
            for ($i = 1; $i <= $totalDays; $i++){
                $pdf->Cell(9, 5, $i, 1, 0, 'C');
                $arrWidth[] = '9';
                $arrAligns[] = 'C';
            }
            $pdf->Ln(-5);
            $pdf->Cell(374, 10, '', 0, 0, 'C');
            $pdf->Cell(10, 10, 'Total', 1, 0, 'C');
            $arrWidth[] = '10';
            $arrAligns[] = 'C';
            $pdf->Ln();
            /* HEADER END */

            /** DATA START */
            $arrHeader = array("10", "30", "55");
            $arrAlign = array("C", "L", "L");
            $arrHeader = array_merge($arrHeader, $arrWidth);
            $arrAlign = array_merge($arrAlign, $arrAligns);
            $pdf->setWidths($arrHeader);
            $pdf->setAligns($arrAlign);

            $pdf->SetFont('Arial', '', 7);
            $no = 0;
            foreach ($employees as $employee) {
                $no++;
                $arrValue = [];
                for ($i = 1; $i <= $totalDays; $i++) {
                    $date = $filterYear.'-'.$filterMonth.'-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                    $arrValue[] = Str::substr($arrData["timesheet"][$employee->id][$date] ?? '', 0, 5) . "\t";
                }
//                $totalTimesheet = $arrData["totalTimesheet"][$employee->id] ?? '';
                $arrValue[] = $arrData["totalTimesheet"][$employee->id] ?? "\t";
                $arrEmployee = array(
                    $no . "\t",
                    $employee->employee_number . "\t",
                    $employee->name . "\t",
                );
                $arrEmployee = array_merge($arrEmployee, $arrValue);
                $pdf->Row($arrEmployee);
            }
            /** DATA END */

            /** FOOTER START */
            $currentY = $pdf->GetY();

            $pdf->SetFont('Arial', '', 9);
            $pdf->SetXY(320, $currentY + 10);
            $pdf->Cell(40,0, 'Jakarta,'.setDate(date('Y-m-d'), 't'), '','','C');
            $pdf->SetXY(320, $currentY + 15);
            $pdf->Cell(40,0, 'Kepala Bagian Tata Usaha', '','','C');

            $getSignature = EmployeeSignatureSetting::active()->first();

            $pdf->SetFont('Arial', 'BU', 9);
            $pdf->SetXY(320, $currentY + 35);
            $pdf->Cell(40,0, '    '.$getSignature->employee->name.'    ', '','','C');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetXY(320, $currentY + 40);
            $pdf->Cell(40,0, 'NIP : '.$getSignature->employee->employee_number, '','','C');
            /**  FOOTER END */
        }

        $pdf->Output();
        exit;
    }

    public function datas($filterMonth, $filterYear){
        $startDate = Carbon::create($filterYear, $filterMonth, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::create($filterYear, $filterMonth, 1)->endOfMonth()->format('Y-m-d');

        $timesheets = DB::table('ess_timesheets')
            ->select('id','date', 'employee_id', 'duration')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $arrTimesheets = [];
        foreach ($timesheets as $timesheet) {
            $arrTimesheets[$timesheet->employee_id][$timesheet->date][$timesheet->id] = convertTimeToSeconds($timesheet->duration);;
        }

        $arrData = [];
        foreach ($arrTimesheets as $employeeId => $arrEmployee) {
            foreach ($arrEmployee as $date => $arrTimesheet) {
                $totalSecond = 0;
                foreach ($arrTimesheet as $timesheetId => $second) {
                    $totalSecond += $second;
                }
                $arrData["timesheet"][$employeeId][$date] = convertMinutesToTime($totalSecond / 60);
                $arrData["timesheetInSeconds"][$employeeId][$date] = $totalSecond;
            }
            $totalSecondEmployee = array_sum($arrData["timesheetInSeconds"][$employeeId]);
            $arrData["totalTimesheet"][$employeeId] = convertMinutesToTime($totalSecondEmployee / 60);
        }

        return $arrData;
    }
}
