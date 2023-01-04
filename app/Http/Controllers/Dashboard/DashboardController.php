<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Attendance\AttendanceCorrection;
use App\Models\Attendance\AttendanceLeave;
use App\Models\Attendance\AttendancePermission;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeePosition;
use DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
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

        return view('dashboard.dashboard', $data);
    }
}
