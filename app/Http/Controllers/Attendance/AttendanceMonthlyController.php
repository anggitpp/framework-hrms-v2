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
                    return $arrData[$row->id][$date]["in"] ?? '';
                });

                $table->addColumn('out_day_' . $i, function ($row) use ($i, $arrData, $date) {
                    return $arrData[$row->id][$date]["out"] ?? '';
                });
            }
            return $table->addIndexColumn()
                ->make();
        }
    }

    private function datas($employees, $filterMonth, $filterYear){
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

                //OUT
                $arrData[$employee->id][$date]["out"] = $arrData[$employee->id][$date]["out"] ?? '';
                $data = $arrData[$employee->id][$date]["out"];
                if(!$isFuture) $data = empty($data) && $carbonDate->isWeekday() ? 'A' : $data;
                $arrData[$employee->id][$date]["out"] = substr($data, 0, 5);
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
            $employees = $sql->orderBy('name')->get();

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
                    $data[$key][$employee->id][$i . "_out"] = $arrData[$employee->id][$date]["out"];
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
}
