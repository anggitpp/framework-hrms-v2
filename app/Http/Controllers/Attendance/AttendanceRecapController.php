<?php

namespace App\Http\Controllers\Attendance;

use App\Exports\Attendance\AttendanceRecapExport;
use App\Http\Controllers\Controller;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceHoliday;
use App\Models\Attendance\AttendancePermission;
use App\Models\Attendance\AttendanceShift;
use App\Models\Attendance\AttendanceWorkSchedule;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeSignatureSetting;
use App\Models\Setting\AppMasterData;
use Carbon\Carbon;
use Excel;
use FPDFTable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Yajra\DataTables\DataTables;

ini_set('memory_limit', '4096M');

class AttendanceRecapController extends Controller
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

        $arrType = ['HK', 'HDR', 'A', 'I', 'C', 'S', 'DL', 'TD'];

        $user = Auth::user();

        Session::put('arrType', $arrType);
        Session::put('user', $user);

        $data['filterMonth'] = $filterMonth;
        $data['filterYear'] = $filterYear;
        $data['arrType'] = $arrType;

        $data['units'] = $units;
        $data['ranks'] = $ranks;

        return view('attendances.attendance-recap.index', $data);
    }

    public function data(Request $request, $filterMonth, $filterYear)
    {
        $arrType = Session::get('arrType');
        $user = Session::get('user');
        $totalDayWithoutWeekend = Carbon::create($filterYear, $filterMonth, 1)->startOfMonth()->diffInDaysFiltered(function(Carbon $date) {
            return !$date->isWeekend();
        }, Carbon::create($filterYear, $filterMonth, 1)->endOfMonth());

        if($request->ajax()){
            $filter = $request->get('search')['value'];

            $filterUnit = $request->get('combo_3');
            $filterRank = $request->get('combo_4');
            $sql = Employee::select(['id', 'name', 'employee_number'])->whereHas('position', function($query) use ($filterUnit, $filterRank, $user){
                if(!$user->hasPermissionTo('lvl3 '.$this->menu_path()))
                    $query->where('leader_id', $user->employee_id);
                if($filterRank) $query->where('rank_id', $filterRank);
                if($filterUnit) $query->where('unit_id', $filterUnit);
            });
            $filteredEmployee = clone $sql;
            $filteredEmployee = $filteredEmployee->get();
            $arrData = $this->datas($filteredEmployee, $filterMonth, $filterYear);
            $table = DataTables::of($sql)
                ->filter(function ($query) use ($filter) {
                    $query->where(function ($query) use ($filter) {
                        $query->where('name', 'like', "%$filter%")
                            ->orWhere('employee_number', 'like', "%$filter%");
                    });
                });
                foreach ($arrType as $key => $type) {
                    $table->addColumn('value_'.$type, function ($row) use ($arrData, $type, $totalDayWithoutWeekend) {
                        if($type == 'HK'){
                            return $arrData[$row->id][$type] == 0 ? $totalDayWithoutWeekend : $arrData[$row->id][$type];
                        }
                        return $arrData[$row->id][$type];
                    });
                }

            return $table->addIndexColumn()
                ->make();
        }
    }

    public function export(Request $request)
    {
        $masters = AppMasterData::whereIn('app_master_category_code', ['EG', 'EP', 'ETP', 'EMU'])->pluck('name', 'id')->toArray();
        $user = Session::get('user');
        $filterMonth = $request->get('filterMonth');
        $filterYear = $request->get('filterYear');

        $totalDayWithoutWeekend = Carbon::create($filterYear, $filterMonth, 1)->startOfMonth()->diffInDaysFiltered(function(Carbon $date) {
            return !$date->isWeekend();
        }, Carbon::create($filterYear, $filterMonth, 1)->endOfMonth());

        /** DATA START */
        $employees = Employee::whereHas('position', function ($query) use ($request, $user) {
            if ($request->get('combo_4')) $query->where('rank_id', $request->get('combo_4'));
            if ($request->get('combo_3')) $query->where('unit_id', $request->get('combo_3'));
            if (!$user->hasPermissionTo('lvl3 ' . $this->menu_path()))
                $query->where('leader_id', $user->employee_id);
        })->get();

        $datas = $this->datas($employees, $filterMonth, $filterYear);

        $data = [];
        $no = 0;
        foreach ($employees as $employee) {
            $no++;
            $positionName = $employee->position->position_id ? AppMasterData::find($employee->position->position_id)->name : '';
            $HK = $datas[$employee->id]['HK'] == 0 ? $totalDayWithoutWeekend : $datas[$employee->id]['HK'];
            $HDR = $datas[$employee->id]['HDR'];
            $A = $datas[$employee->id]['A'];
            $I = $datas[$employee->id]['I'];
            $C = $datas[$employee->id]['C'];
            $S = $datas[$employee->id]['S'];
            $DL = $datas[$employee->id]['DL'];
            $TD = $datas[$employee->id]['TD'];
            $data[$employee->id] = [
                $no,
                $employee->name,
                $positionName,
                $HK,
                $HDR,
                $A,
                $I,
                $C,
                $S,
                $DL,
                $TD,
            ];
        }

        return Excel::download(new AttendanceRecapExport(
            [
                'data' => $data,
                'headerTitle' => 'Data Rekap Absen',
                'headerSubtitle' => "PERIODE : ".numToMonth($request->get('filterMonth')).' '.$request->get('filterYear'),
                'additional_title' => $request->get('combo_3') ? 'UNIT : '.$masters[$request->get('combo_3')] : 'SEMUA UNIT',
            ]
        ), 'Rekap Absen.xlsx');
    }

    public function datas($employees, $filterMonth, $filterYear){
        $startDate = Carbon::create($filterYear, $filterMonth, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::create($filterYear, $filterMonth, 1)->endOfMonth()->format('Y-m-d');

        $arrData = [];
        $arrSchedules = [];
        $arrAttendances = [];

        $schedules = AttendanceWorkSchedule::whereBetween('date', [$startDate, $endDate])->select(['employee_id', 'date'])->whereNot('shift_id', '0')->get();
        $attendances = Attendance::whereBetween('start_date', [$startDate, $endDate])->select(['id', 'duration', 'employee_id', 'type', 'type_id', 'start_date', 'start_time', 'end_time'])->get();

        foreach ($schedules as $schedule) {
            $arrSchedules[$schedule->employee_id][$schedule->date] = $schedule;
        }

        foreach ($attendances as $attendance) {
            $arrAttendances[$attendance->employee_id][$attendance->start_date] = $attendance;
        }

        $dateNow = Carbon::now()->format('Y-m-d');
        $totalWeekdayInMonth = Carbon::create($filterYear, $filterMonth, 1)->startOfMonth()->diffInDaysFiltered(function(Carbon $date) {
            return !$date->isWeekend();
        }, Carbon::create($filterYear, $filterMonth, 1)->endOfMonth());

        $holidays = AttendanceHoliday::whereBetween('start_date', [$startDate, $endDate])->whereBetween('end_date', [$startDate, $endDate])->get();
        //get list of date from holidays
        $totalHolidays = 0;
        foreach ($holidays as $holiday) {
            $startDate = Carbon::parse($holiday->start_date);
            $endDate = Carbon::parse($holiday->end_date);
            $diff = $startDate->diffInDays($endDate);
            for ($i = 0; $i <= $diff; $i++) {
                $totalHolidays++;
                $startDate->addDay();
            }
        }

        foreach ($employees as $employee) {
            $arrData[$employee->id]['A'] = 0;
            $arrData[$employee->id]['I'] = 0;
            $arrData[$employee->id]['C'] = 0;
            $arrData[$employee->id]['S'] = 0;
            $arrData[$employee->id]['DL'] = 0;
            $arrData[$employee->id]['HDR'] = 0;
            $arrData[$employee->id]['HK'] = $totalWeekdayInMonth - $totalHolidays;
            $arrData[$employee->id]['TD'] = 0;

            for ($i = 1; $i <= Carbon::create($filterYear, $filterMonth, 1)->daysInMonth; $i++) {
                $date = Carbon::create($filterYear, $filterMonth, $i)->format('Y-m-d');
                $carbonDate = Carbon::create($date);

                if(isset($arrSchedules[$employee->id][$date]) && !isset($arrAttendances[$employee->id][$date])) {
                    if($carbonDate < $dateNow) $arrData[$employee->id]['A']++;
                }else if($carbonDate->isWeekday() && !isset($arrAttendances[$employee->id][$date])) {
                    if($carbonDate < $dateNow) $arrData[$employee->id]['A']++;
                }else{
                    $data = $arrAttendances[$employee->id][$date] ?? null;
                    if($data){
                        if($data->type == 'C'){
                            $arrData[$employee->id]['C']++;
                        }else if($data->type == 'I'){
                            $getDataIzin = AttendancePermission::find($data->type_id);
                            if($getDataIzin->category_id == 30 || $getDataIzin->category_id == 31) {
                                $arrData[$employee->id]['S']++;
                            }else {
                                $arrData[$employee->id]['I']++;
                            }
                        }else{
                            if($data->type == '3' || $data->type == '4' || $data->type == '5') $arrData[$employee->id]['DL']++;
                            $arrData[$employee->id]['HDR']++;
                        }
                    }
                }
            }
        }

        return $arrData;
    }
}
