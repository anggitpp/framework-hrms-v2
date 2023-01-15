<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceCorrection;
use App\Models\Attendance\AttendanceLeave;
use App\Models\Attendance\AttendanceShift;
use App\Models\Attendance\AttendanceWorkSchedule;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeePosition;
use App\Models\ESS\EssTimesheet;
use App\Models\Setting\AppParameter;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ESSDashboardController extends Controller
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

        $defaultShift = AttendanceShift::orderBy('id')->first();

        $attendance = Attendance::select(['type', 'start_date', 'id', 'start_time', 'end_time'])
            ->whereEmployeeId($user->employee_id)
            ->whereBetween('start_date', [$startDate, $endDate])
            ->orderBy('start_date')
            ->get();

        $schedules = AttendanceWorkSchedule::whereEmployeeId($user->employee_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        $arrTotalAttendance = [];
        $arrAttendance = [];
        $arrLateNotAttendance = [];
        foreach ($attendance as $key => $value) {
            $arrTotalAttendance[$value->start_date][$value->type] = isset($arrTotalAttendance[$value->start_date][$value->type]) ? $arrTotalAttendance[$value->start_date][$value->type] + 1 : 1;
            $arrAttendance[$value->start_date] = $value->id;

            $schedule = $schedules->where('date', $value->start_date)->first();
            if(isset($schedule)){
                if($schedule->start_time <= $value->start_time){
                    $arrLateNotAttendance['late'] = isset($arrLateNotAttendance['late']) ? $arrLateNotAttendance['late'] + 1 : 1;
                }
            }else{
                if($defaultShift->start <= $value->start_time){
                    $arrLateNotAttendance['late'] = isset($arrLateNotAttendance['late']) ? $arrLateNotAttendance['late'] + 1 : 1;
                }
            }

            if(empty($value->start_time) && !empty($value->end_time)){
                $arrLateNotAttendance['notAttendanceStart'] = isset($arrLateNotAttendance['notAttendanceStart']) ? $arrLateNotAttendance['notAttendanceStart'] + 1 : 1;
            }else if(!empty($value->start_time) && empty($value->end_time)){
                $arrLateNotAttendance['notAttendanceEnd'] = isset($arrLateNotAttendance['notAttendanceEnd']) ? $arrLateNotAttendance['notAttendanceEnd'] + 1 : 1;
            }
        }

        $arrDate = [];
        foreach ($period as $date) {
            if($date->isWeekday()) {
                $arrDate[] = $date->format('Y-m-d');
            }
        }

        $dateNow = Carbon::now()->format('Y-m-d');
        foreach ($arrDate as $date){
            if($date <= $dateNow) {
                if (!isset($arrAttendance[$date])) {
                    $arrTotalAttendance[$date]['A'] = isset($arrTotalAttendance[$date]['A']) ? $arrTotalAttendance[$date]['A'] + 1 : 1;
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


        $timesheets = EssTimesheet::whereEmployeeId($user->employee_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        $arrTotalDurationTimesheet = [];
        foreach ($timesheets as $timesheet){
            list($year, $month, $day) = explode('-', $timesheet->date);
            //sum duration
            $arrTotalDurationTimesheet[$day] = isset($arrTotalDurationTimesheet[$day]) ? convertTimeToSeconds($timesheet->duration) + $arrTotalDurationTimesheet[$day] : convertTimeToSeconds($timesheet->duration);
        }

        $data['totalAttendance'] = $arrTotalRecapByMonth;
        $data['arrLateNotAttendance'] = $arrLateNotAttendance;
        $data['arrTotalDurationTimesheet'] = $arrTotalDurationTimesheet;
        $data['arrTotalDurationInMonthTimesheet'] = array_sum($arrTotalDurationTimesheet);

        $data['filterMonth'] = $currentMonth;
        $data['filterYear'] = $currentYear;

        return view('ess.dashboard.dashboard', $data);
    }
}
