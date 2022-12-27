<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance\AttendanceLeave;
use App\Models\Attendance\AttendanceLeaveMaster;
use App\Models\Attendance\AttendancePermission;
use App\Models\Employee\Employee;
use App\Models\Setting\AppMasterData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SubmissionController extends Controller
{
    public string $leavePath;
    public string $permissionPath;

    public function __construct()
    {
        $this->leavePath = '/uploads/attendance/leave/';
        $this->permissionPath = '/uploads/attendance/permission/';
    }
    public function getCategory()
    {
        if (Auth::check()) {
            $categories = AttendanceLeaveMaster::whereStatus('t')->select(['id', 'name'])->get();

            if ($categories) {
                return response()->json([
                    'message' => 'Success',
                    'data' => $categories,
                ]);
            } else {
                return response()->json([
                    'message' => 'Failed',
                    'data' => null,
                ], 400);
            }
        }else{
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }
    }

    public function store(Request $request){
        if (Auth::check()) {
            $user = Auth::user();
            try {
                $urlStorage = url('storage');
                $filename = $request->get('filename') ? Str::replace($urlStorage, '', $request->get('filename')) : '';

                //CHECK VALIDATION
                $master = AttendanceLeaveMaster::find($request->get('category_id'));

                $startDate = $request->input('start_date');
                $endDate = $request->input('end_date');
                $amount = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;

                if($master->balance != 0) {
                    $submittedAmount = AttendanceLeave::where('employee_id', $user->employee_id)
                        ->where('leave_master_id', $request->get('category_id'))
                        ->where('approved_status', '!=', 'f')
                        ->sum('amount');

                    $employee = Employee::find($user->employee_id);

                    $master->balance = $master->balance - $submittedAmount;

                    if ($master->gender != "a") {
                        if ($master->gender != $employee->gender) {
                            $master->balance = 0;
                        }
                    }

                    $remaining = $master->balance - $amount;

                    if ($remaining < 0) {
                        return response()->json([
                            'message' => 'Gagal, sisa cuti anda tidak mencukupi',
                        ], 400);
                    }
                }

                $getLastNumber = AttendanceLeave::whereYear('created_at', date('Y'))
                    ->orderBy('number', 'desc')
                    ->pluck('number')
                    ->first() ?? 0;
                //SET FORMAT FOR NUMBER LEAVE
                $lastNumber = Str::padLeft(intval(Str::substr($getLastNumber,0,4)) + 1, '4', '0').'/'.numToRoman(date('m')).'/IC/'.date('Y');

                AttendanceLeave::create([
                    'number' => $lastNumber,
                    'employee_id' => $user->employee_id,
                    'leave_master_id' => $request->input('category_id'),
                    'date' => date('Y-m-d'),
                    'start_date' => $request->get('start_date'),
                    'end_date' => $request->get('end_date'),
                    'filename' => $filename,
                    'balance' => $master->balance,
                    'amount' => $amount,
                    'remaining' => $remaining ?? 0,
                    'description' => $request->input('description'),
                    'approved_status' => 'p'
                ]);

                return response()->json([
                    'message' => 'Success',
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Failed',
                    'data' => $e->getMessage()
                ]);
            }
        }
    }

    public function edit(int $id){
        if (Auth::check()) {
            try {
                $leave = AttendanceLeave::find($id);
                $data['id'] = $leave->id;
                $data['category_id'] = $leave->leave_master_id;
                $data['start_date'] = $leave->start_date;
                $data['end_date'] = $leave->end_date;
                $data['description'] = $leave->description;
                $data['filename'] = $leave->filename;

                if($data){
                    return response()->json([
                        'message' => 'Success',
                        'data' => $data
                    ]);
                }else{
                    return response()->json([
                        'message' => 'Failed',
                        'data' => null
                    ], 400);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Failed',
                    'data' => $e->getMessage()
                ]);
            }
        }else{
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }
    }

    public function update(Request $request, int $id)
    {
        if (Auth::check()) {
            $user = Auth::user();
            try {
                $master = AttendanceLeaveMaster::find($request->get('category_id'));

                $startDate = $request->input('start_date');
                $endDate = $request->input('end_date');
                $amount = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;

                if($master->balance != 0) {
                    $submittedAmount = AttendanceLeave::where('employee_id', $user->employee_id)
                        ->where('leave_master_id', $request->get('category_id'))
                        ->where('approved_status', '!=', 'f')
                        ->where('id', '!=', $id)
                        ->sum('amount');

                    $employee = Employee::find($user->employee_id);

                    $master->balance = $master->balance - $submittedAmount;

                    if ($master->gender != "a") {
                        if ($master->gender != $employee->gender) {
                            $master->balance = 0;
                        }
                    }

                    $remaining = $master->balance - $amount;

                    if ($remaining < 0) {
                        return response()->json([
                            'message' => 'Gagal, sisa cuti anda tidak mencukupi',
                        ], 400);
                    }
                }

                $leave = AttendanceLeave::find($id);
                if($leave){
                    $urlStorage = url('storage');
                    $filename = $request->get('filename') ? Str::replace($urlStorage, '', $request->get('filename')) : $leave->filename;
                    $leave->update([
                        'leave_master_id' => $request->get('category_id'),
                        'start_date' => $request->get('start_date'),
                        'end_date' => $request->get('end_date'),
                        'description' => $request->get('description'),
                        'balance' => $master->balance,
                        'amount' => $amount,
                        'remaining' => $remaining ?? 0,
                        'filename' => $filename,
                    ]);

                    return response()->json([
                        'message' => 'Success',
                    ]);
                }else{
                    return response()->json([
                        'message' => 'Failed, data not found.',
                    ], 400);
                }

            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Failed '.$e->getMessage(),
                ]);
            }
        }else{
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }
    }

    public function delete(int $id)
    {
        if (Auth::check()) {
            try {
                $leave = AttendanceLeave::find($id);
                $leave->delete();

                return response()->json([
                    'message' => 'Success',
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Failed '.$e->getMessage(),
                ]);
            }
        }else{
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }
    }

    public function uploadImage(Request $request){

        $pathType = $this->leavePath;
        if(Auth::check()){
            $user = Auth::user();
            $employee = Employee::find($user->employee_id);
            $picture = uploadFile(
                $request->file('image'),
                Str::slug($employee->name).'_'.time(),
                $pathType, true);
            $picture = url('storage'.$picture);

            return response()->json([
                'message' => 'Success',
                'data' => $picture
            ]);
        }else{
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }
}
