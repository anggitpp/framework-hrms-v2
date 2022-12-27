<?php

namespace App\Http\Controllers\Attendance;

use App\Exports\Attendance\AttendanceDurationRecapExport;
use App\Http\Controllers\Controller;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceWorkSchedule;
use App\Models\Employee\Employee;
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

class AttendanceDurationRecapController extends Controller
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

        $units = AppMasterData::whereAppMasterCategoryCode('EMU')->pluck('name', 'id')->toArray();
        $ranks = AppMasterData::whereAppMasterCategoryCode('EP')->pluck('name', 'id')->toArray();

        $user = Auth::user();

        $totalDays = Carbon::create($filterYear, $filterMonth, 1)->daysInMonth;

        $data['units'] = $units;
        $data['ranks'] = $ranks;
        $data['filterMonth'] = $filterMonth;
        $data['filterYear'] = $filterYear;
        $data['totalDays'] = $totalDays;

        \Session::put('user', $user);

        return view('attendances.duration-recap.index', $data);
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

                $table->addColumn('day_' . $i, function ($row) use ($i, $arrData, $date) {
                    return $arrData[$row->id][$date];
                });
            }
            return $table->addIndexColumn()
                ->make();
        }
    }

    public function export(Request $request)
    {
        $user = \Session::get('user');
        $filterMonth = $request->get('filterMonth');
        $filterYear = $request->get('filterYear');
        $totalDays = Carbon::create($filterYear, $filterMonth, 1)->daysInMonth;

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
            $employees = $sql->select(['t1.id', 't1.name', 't1.employee_number'])->orderBy('t1.name')->get();

            $arrData = $this->datas($employees, $filterMonth, $filterYear);

            $no = 0;
            foreach ($employees as $employee) {
                $no++;

                $data[$key][$employee->id] = [
                    'no' => $no,
                    'employee_number' => $employee->employee_number . " ",
                    'name' => $employee->name,
                ];

                for ($i = 1; $i <= $totalDays; $i++) {
                    $date = $filterYear . '-' . str_pad($filterMonth, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                    $data[$key][$employee->id][(string)$i] = $arrData[$employee->id][$date];
                }
            }
        }

        return Excel::download(new AttendanceDurationRecapExport(
            [
                'data' => $data,
                'headerTitle' => 'Data Rekap Durasi',
                'headerSubtitle' => "PERIODE : ".numToMonth($request->get('filterMonth')).' '.$request->get('filterYear'),
                'totalDays' => Carbon::create($request->get('filterYear').'-'.$request->get('filterMonth'))->daysInMonth,
                'units' => $units,
            ]
        ), 'Rekap Durasi.xlsx');
    }

    public function pdf(Request $request)
    {
        $user = Session::get('user');
        $filterMonth = $request->get('filterMonth');
        $filterYear = $request->get('filterYear');
        $totalDays = Carbon::create($filterYear, $filterMonth, 1)->daysInMonth;

        $pdf = new FPDFTable('L', 'mm', ['250', '400']);
        $pdf->SetAutoPageBreak(TRUE);
        $pdf->SetTitle('REKAP ABSEN SEMUA UNIT ' . strtoupper(numToMonth($request->get('filterMonth'))) . ' ' . $request->get('filterYear'));

        $units = AppMasterData::whereAppMasterCategoryCode('EMU');
        //FILTER IF ANY UNIT
        if($request->get('combo_3') && $request->get('combo_3') != 'undefined') $units->whereId($request->get('combo_3'));
        $filterRank = $request->get('combo_4');

        $units = $units->get();
        foreach ($units as $unit) {
            $employees = Employee::select(['id', 'name', 'employee_number'])
                ->whereHas('position', function($query) use ($filterRank, $user, $unit){
                    $query->select(['id', 'employee_id', 'position_id']);
                    $query->where('unit_id', $unit->id);
                    if($filterRank && $filterRank != 'undefined') $query->where('rank_id', $filterRank);
                });
            $employees = $employees->orderBy('name')->get();
            $arrData = $this->datas($employees, $filterMonth, $filterYear);

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
                    $date = $filterYear.'-'.str_pad($filterMonth, 2, '0', STR_PAD_LEFT).'-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                    $arrValue[] = $arrData[$employee->id][$date] . "\t";
                }
                $arrEmployee = array(
                    $no . "\t",
                    $employee->employee_number . "\t",
                    $employee->name . "\t",
                );
                $arrEmployee = array_merge($arrEmployee, $arrValue);
                $pdf->Row($arrEmployee);
            }
        }

        $pdf->Output();
        exit;
    }

    public function datas($employees, $filterMonth, $filterYear)
    {
        $startDate = Carbon::create($filterYear, $filterMonth, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::create($filterYear, $filterMonth, 1)->endOfMonth()->format('Y-m-d');

        $schedules = AttendanceWorkSchedule::whereBetween('date', [$startDate, $endDate])->select(['employee_id', 'date'])->whereNot('shift_id', '0')->get();
        $attendances = Attendance::whereBetween('start_date', [$startDate, $endDate])->select(['duration', 'employee_id', 'type', 'start_date'])->get();

        $arrSchedules = [];
        foreach ($schedules as $schedule) {
            $arrSchedules[$schedule->employee_id][$schedule->date] = $schedule;
        }

        $arrAttendances = [];
        foreach ($attendances as $attendance) {
            $arrAttendances[$attendance->employee_id][$attendance->start_date] = $attendance;
        }

        $arrData = [];
        $dateNow = Carbon::now()->format('Y-m-d');
        for ($i = 1; $i <= Carbon::create($filterYear, $filterMonth, 1)->daysInMonth; $i++) {
            $date = Carbon::create($filterYear, $filterMonth, $i)->format('Y-m-d');
            $carbonDate = Carbon::create($date);
            $isFuture = false;
            if($dateNow < $date) $isFuture = true;
            foreach ($employees as $employee) {
                if(isset($arrSchedules[$employee->id][$date]) && !isset($arrAttendances[$employee->id][$date])) {
                    $arrData[$employee->id][$date] = $carbonDate < $dateNow ? "A" : '';
                }else{
                    $data = $arrAttendances[$employee->id][$date] ?? null;
                    if($data){
                        if($data->type == 'C'){
                            $arrData[$employee->id][$date] = 'C';
                        }else if($data->type == 'I'){
                            $arrData[$employee->id][$date] = 'I';
                        }else{
                            $arrData[$employee->id][$date] = $data->duration;
                        }
                    }
                }

                $data = $arrData[$employee->id][$date] ?? '';
                if(!$isFuture) $data = empty($data) && $carbonDate->isWeekday() ? 'A' : $data;

                $arrData[$employee->id][$date] = substr($data, 0, 5);
            }
        }

        return $arrData;
    }
}
