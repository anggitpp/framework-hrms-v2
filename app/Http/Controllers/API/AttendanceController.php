<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance\Attendance;
use App\Models\Employee\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public string $photoPath;

    public function __construct()
    {
        $this->photoPath = '/uploads/attendance/daily/';
    }

    public function attendance(){
        if (Auth::check()) {
            $user = Auth::user();
            $data = Attendance::select(['id', 'type', 'start_time', 'start_latitude', 'start_longitude', 'start_image', 'start_address', 'end_time', 'end_latitude', 'end_longitude', 'end_image', 'end_address'])
                ->whereStartDate(date('Y-m-d'))->whereEmployeeId($user->employee_id)->first();
            if($data) {
                return response()->json([
                    'message' => 'Success',
                    'data' => $data
                ]);
            }else{
                return response()->json([
                    'message' => 'Success',
                    'data' => null
                ]);
            }
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    public function save(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();

            $urlStorage = url('storage');
            $image = $request->get('image') ? Str::replace($urlStorage, '', $request->get('image')) : '';

            try {
                if($request->get('inout') == 'in'){
                    $arrUpdate = [
                        'start_time' => $request->get('time'),
                        'start_latitude' => $request->get('latitude'),
                        'start_longitude' => $request->get('longitude'),
                        'start_image' => $image,
                        'start_address' => $request->get('address'),
                    ];
                }else{
                    $arrUpdate = [
                        'end_date' => date('Y-m-d'),
                        'end_time' => $request->get('time'),
                        'end_latitude' => $request->get('latitude'),
                        'end_longitude' => $request->get('longitude'),
                        'end_image' => $image,
                        'end_address' => $request->get('address'),
                    ];
                }

                $attendance = Attendance::updateOrCreate([
                    'employee_id' => $user->employee_id,
                    'start_date' => $request->get('date'),
                    'type' => $request->get('type')
                ], $arrUpdate);

                if(!empty($attendance->start_time) && !empty($attendance->end_time)){
                    $attendance->duration = Carbon::parse($attendance->start_time)->diff(Carbon::parse($attendance->end_time))->format('%H:%I:%S');
                    $attendance->save();
                }

                return response()->json([
                    'message' => 'Success',
                    'data' => $attendance
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

    public function uploadImage(Request $request){
        if(Auth::check()){
            $user = Auth::user();
            $employee = Employee::find($user->employee_id);
            $picture = uploadFile(
                $request->file('image'),
                Str::slug($employee->name).'_'.time(),
                $this->photoPath, true);
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
