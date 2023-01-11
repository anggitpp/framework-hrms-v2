<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceCorrection;
use App\Models\Attendance\AttendanceLeave;
use App\Models\Attendance\AttendancePermission;
use App\Models\Attendance\AttendanceWorkSchedule;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeePosition;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(){
        $currentMonth = date('m');
        $currentYear = date('Y');

        $startDate = Carbon::create($currentYear, $currentMonth, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::create($currentYear, $currentMonth, 1)->endOfMonth()->format('Y-m-d');

        $period = CarbonPeriod::create($startDate, $endDate);

        $data['totalEmployee'] = Employee::count();
        $data['totalEmployeeByGender'] = Employee::active()->select(['gender', DB::raw('count(id) as totalEmployee')])->groupBy('gender')->pluck('totalEmployee', 'gender');
        $data['totalEmployeeByCategories'] = EmployeePosition::select(['employee_type_id', DB::raw('count(employee_positions.id) as totalEmployee'), 't2.name'])
            ->join('app_master_data as t2', 'employee_type_id', 't2.id')
            ->where('employee_positions.status', 't')
            ->whereNot('employee_type_id', '0')
            ->groupBy('employee_type_id')
            ->pluck('totalEmployee', 'name');
        $data['totalLeaves'] = AttendanceLeave::count();
        $data['totalPermissions'] = AttendancePermission::count();
        $data['totalCorrections'] = AttendanceCorrection::count();
        $data['totalSubmission'] = $data['totalLeaves'] + $data['totalPermissions'] + $data['totalCorrections'];

        $attendance = Attendance::select(['type', 'start_date', 'id', 'employee_id'])->whereBetween('start_date', [$startDate, $endDate])->orderBy('start_date')->get();

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
        $employees = Employee::select('id')->get();
        foreach ($employees as $employee){
            foreach ($arrDate as $date){
                if($date <= $dateNow) {
                    if (!isset($arrAttendance[$date][$employee->id])) {
                        $arrTotalAttendance[$date]['A'] = isset($arrTotalAttendance[$date]['A']) ? $arrTotalAttendance[$date]['A'] + 1 : 1;
                    }
                }
            }
        }

        $data['totalAttendanceByDay'] = $arrTotalAttendance;

        return view('dashboard.dashboard', $data);
    }
}
