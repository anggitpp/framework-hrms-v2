<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceCorrection;
use App\Models\Attendance\AttendanceLeave;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeePosition;
use App\Models\Setting\AppParameter;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request){
        $user = Auth::user();

        $currentMonth = $request->get('filterMonth') ?? date('m');
        $currentYear = $request->get('filterYear') ?? date('Y');

        $startDate = Carbon::create($currentYear, $currentMonth, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::create($currentYear, $currentMonth, 1)->endOfMonth()->format('Y-m-d');

        $period = CarbonPeriod::create($startDate, $endDate);

        $data['totalEmployee'] = Employee::whereHas('position', function ($query) use ($user) {
            if(!$user->hasPermissionTo('lvl3 '.$this->menu_path())) $query->where('leader_id', $user->employee_id);
        })
            ->active()
            ->count();

        $data['totalEmployeeByGender'] = Employee::whereHas('position', function ($query) use ($user) {
            if(!$user->hasPermissionTo('lvl3 '.$this->menu_path())) $query->where('leader_id', $user->employee_id);
        })
            ->active()
            ->select(['gender', DB::raw('count(id) as totalEmployee')])
            ->groupBy('gender')
            ->pluck('totalEmployee', 'gender');

        $data['totalEmployeeByCategories'] = EmployeePosition::whereHas('employee', function($query){
            $query->active();
        })
            ->select(['employee_type_id', DB::raw('count(employee_positions.id) as totalEmployee'), 't2.name'])
            ->join('app_master_data as t2', function ($join) use ($user) {
                $join->on('employee_type_id', 't2.id');
                if (!$user->hasPermissionTo('lvl3 ' . $this->menu_path())) $join->where('leader_id', $user->employee_id);
            })
            ->where('employee_positions.status', 't')
            ->whereNot('employee_type_id', '0')
            ->groupBy('employee_type_id')
            ->pluck('totalEmployee', 'name');

        $getIdSick = AppParameter::whereCode('CS')->first()->value;
        $data['totalLeaves'] = AttendanceLeave::whereHas('employee.position', function ($query) use ($user) {
            if (!$user->hasPermissionTo('lvl3 ' . $this->menu_path())) $query->where('leader_id', $user->employee_id);
        })
            ->whereNot('leave_master_id', $getIdSick)
            ->whereBetween('start_date', [$startDate, $endDate])
            ->count();

        $data['totalSicks'] = AttendanceLeave::whereHas('employee.position', function ($query) use ($user) {
            if (!$user->hasPermissionTo('lvl3 ' . $this->menu_path())) $query->where('leader_id', $user->employee_id);
        })
            ->whereLeaveMasterId($getIdSick)
            ->whereBetween('start_date', [$startDate, $endDate])
            ->count();

        $data['totalCorrections'] = AttendanceCorrection::whereHas('employee.position', function ($query) use ($user) {
            if (!$user->hasPermissionTo('lvl3 ' . $this->menu_path())) $query->where('leader_id', $user->employee_id);
        })
            ->whereBetween('date', [$startDate, $endDate])
            ->count();

        $data['totalSubmission'] = $data['totalLeaves'] + $data['totalSicks'] + $data['totalCorrections'];

        $attendance = Attendance::select(['type', 'start_date', 'id', 'employee_id'])
            ->whereHas('employee.position', function ($query) use ($user) {
                if (!$user->hasPermissionTo('lvl3 ' . $this->menu_path())) $query->where('leader_id', $user->employee_id);
            })
            ->whereBetween('start_date', [$startDate, $endDate])
            ->orderBy('start_date')
            ->get();

        $arrTotalAttendance = [];
        $arrAttendance = [];
        foreach ($attendance as $key => $value) {
            $arrTotalAttendance[$value->start_date][$value->type] = isset($arrTotalAttendance[$value->start_date][$value->type]) ? $arrTotalAttendance[$value->start_date][$value->type] + 1 : 1;
            $arrAttendance[$value->start_date][$value->employee_id] = $value->id;
        }

        $arrDate = [];
        foreach ($period as $date) {
            if($date->isWeekday()) {
                $arrDate[] = $date->format('Y-m-d');
            }
        }

        $dateNow = Carbon::now()->format('Y-m-d');
        $employees = Employee::select('id')->whereHas('position', function ($query) use ($user) {
            if(!$user->hasPermissionTo('lvl3 '.$this->menu_path())) $query->where('leader_id', $user->employee_id);
        })
            ->active()
            ->get();
        foreach ($employees as $employee){
            foreach ($arrDate as $date){
                if($date <= $dateNow) {
                    if (!isset($arrAttendance[$date][$employee->id])) {
                        $arrTotalAttendance[$date]['A'] = isset($arrTotalAttendance[$date]['A']) ? $arrTotalAttendance[$date]['A'] + 1 : 1;
                    }
                }
            }
        }

        $arrTotalRecap = [];
        foreach ($period as $date) {
            $arrTotalRecap['A'][] = isset($arrTotalAttendance[$date->format('Y-m-d')]['A']) ? $arrTotalAttendance[$date->format('Y-m-d')]['A'] : 0;
            $arrTotalRecap['P'][] = (isset($arrTotalAttendance[$date->format('Y-m-d')]['1']) ? $arrTotalAttendance[$date->format('Y-m-d')]['1'] : 0) + (isset($arrTotalAttendance[$date->format('Y-m-d')]['2']) ? $arrTotalAttendance[$date->format('Y-m-d')]['2'] : 0) + (isset($arrTotalAttendance[$date->format('Y-m-d')]['3']) ? $arrTotalAttendance[$date->format('Y-m-d')]['3'] : 0) + (isset($arrTotalAttendance[$date->format('Y-m-d')]['4']) ? $arrTotalAttendance[$date->format('Y-m-d')]['4'] : 0) + (isset($arrTotalAttendance[$date->format('Y-m-d')]['5']) ? $arrTotalAttendance[$date->format('Y-m-d')]['5'] : 0);
            $arrTotalRecap['C'][] = isset($arrTotalAttendance[$date->format('Y-m-d')]['C']) ? $arrTotalAttendance[$date->format('Y-m-d')]['C'] : 0;
            $arrTotalRecap['S'][] = isset($arrTotalAttendance[$date->format('Y-m-d')]['S']) ? $arrTotalAttendance[$date->format('Y-m-d')]['S'] : 0;
        }

        $arrTotalRecapByMonth['A'] = array_sum($arrTotalRecap['A']);
        $arrTotalRecapByMonth['P'] = array_sum($arrTotalRecap['P']);
        $arrTotalRecapByMonth['C'] = array_sum($arrTotalRecap['C']);
        $arrTotalRecapByMonth['S'] = array_sum($arrTotalRecap['S']);

        $data['totalAttendanceByDay'] = $arrTotalRecap;
        $data['totalRecapByMonth'] = $arrTotalRecapByMonth;

        $data['filterMonth'] = $currentMonth;
        $data['filterYear'] = $currentYear;

        return view('dashboard.dashboard', $data);
    }
}
