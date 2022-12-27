<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceLeave;
use App\Models\Attendance\AttendanceLeaveMaster;
use App\Models\Attendance\AttendanceLocationSetting;
use App\Models\Attendance\AttendancePermission;
use App\Models\Attendance\AttendanceShift;
use App\Models\Attendance\AttendanceWorkSchedule;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeePosition;
use App\Models\Setting\AppMasterData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function getInitialData()
    {
        if (Auth::check()) {
            try {
                $user = Auth::user();
                $employee = Employee::select(['id', 'name', 'email', 'employee_number', 'mobile_phone_number', 'photo'])->find($user->employee_id);
                $position = EmployeePosition::active()->where('employee_id', $user->employee_id)->first();

                $dateNow = date('Y-m-d');

                $user->employee_number = $employee->employee_number;
                $user->mobile_phone_number = $employee->mobile_phone_number;
                $user->position = AppMasterData::find($position->position_id)->name;
                $user->grade = AppMasterData::find($position->grade_id)->name;
                $user->unit = AppMasterData::find($position->unit_id)->name;
                $user->photo = url('storage'.$user->photo);
                $data['profile'] = $user;
                $attendance = Attendance::select(['start_time', 'end_time'])->whereEmployeeId($user->employee_id)->whereDate('start_date', $dateNow)->first();
                $data['attendance'] = $attendance;

                $data['isCanWFH'] = false;
                $settingLocation = AttendanceLocationSetting::whereLocationId($position->location_id)->first();
                if($settingLocation) $data['isCanWFH'] = $settingLocation->wfh == "t";

                $schedule = AttendanceWorkSchedule::whereEmployeeId($user->employee_id)->whereDate('date', $dateNow)->whereNot('shift_id', '0')->first();
                if(!$schedule){
                    $schedule = AttendanceShift::find($position->shift_id);
                    $schedule->start_time = $schedule->start;
                    $schedule->end_time = $schedule->end;
                }
                $data['startShift'] = $schedule->start_time ?? '';
                $data['endShift'] = $schedule->end_time ?? '';
                $data['textCheckout'] = '';
                if($attendance) {
                    $data['textCheckout'] = $attendance->type == '2' ? 'Anda belum melakukan Check Out untuk absen WFH' : 'Anda belum melakukan Check Out untuk absen Dinas Luar Pagi sampai Sore' ?? '';
                }

                return response()->json([
                    'message' => 'Success',
                    'data' => $data ?? ''
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Failed',
                    'data' => $e->getMessage()
                ]);
            }

        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    public function getAttendances(Request $request){
        if (Auth::check()) {
            $month = $request->get('month');
            $year = $request->get('year');

            $startDate = Carbon::create($year, $month, 1)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d');

            $attendance = Attendance::select(['id',
            DB::raw("CASE
            WHEN type = '4' THEN 3
            WHEN type = '5' THEN 3
            ELSE type END as type"),
            'start_date',
            'end_date',
            'start_time',
            'end_time',
            'start_address',
            'end_address',
            'type_id',
            DB::raw('NULL as type_description'),
            DB::raw('NULL as type_reason'),
            DB::raw('NULL as filename'),
            DB::raw("'t' as approved_status")])
            ->whereEmployeeId(Auth::user()->employee_id)
            ->whereNot('type', 'I')
            ->whereNot('type', 'C')
            ->whereBetween('start_date', [$startDate, $endDate]);

            $leaves = AttendanceLeave::select([
            'id',
            DB::raw("'C' as type"),
            'start_date',
            'end_date',
            DB::raw('NULL AS start_time'),
            DB::raw('NULL AS end_time'),
            DB::raw('NULL AS start_address'),
            DB::raw('NULL AS end_address'),
            DB::raw('id AS type_id'),
            'leave_master_id as type_description',
            'description as type_reason',
            'filename',
            'approved_status'])
            ->whereEmployeeId(Auth::user()->employee_id)
            ->whereBetween('start_date', [$startDate, $endDate]);

            $permissions = AttendancePermission::select([
                'id',
                DB::raw("'I' as type"),
                'start_date',
                'end_date',
                DB::raw('NULL AS start_time'),
                DB::raw('NULL AS end_time'),
                DB::raw('NULL AS start_address'),
                DB::raw('NULL AS end_address'),
                DB::raw('id AS type_id'),
                'category_id as type_description',
                'description as type_reason',
                'filename',
                'approved_status'])
                ->whereEmployeeId(Auth::user()->employee_id)
                ->whereBetween('start_date', [$startDate, $endDate]);

            $attendances = $attendance->union($leaves)->union($permissions)->orderBy('start_date', 'desc')->simplePaginate(10);

            foreach ($attendances as $key => $value) {
                $value->type_description = '';
                if($value->type == 'I'){
                    $value->type = 4;
                    $permission = AttendancePermission::find($value->type_id);
                    $value->type_description = AppMasterData::find($permission->category_id)->name ?? '';
                }else if($value->type == 'C'){
                    $value->type = 4;
                    $leave = AttendanceLeave::find($value->type_id);
                    $value->type_description = AttendanceLeaveMaster::find($leave->leave_master_id)->name ?? '';
                }
                $value->type = (int)$value->type;
            }

            return response()->json([
                'message' => 'Success',
                'data' => $attendances
            ]);
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }
}


