<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Employee\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public string $permissionPath;

    public function __construct()
    {
        $this->permissionPath = '/uploads/user/';
    }

    public function uploadImage(Request $request){

        if(Auth::check()){
            try {
                $user = Auth::user();
                $employee = Employee::find($user->employee_id);
                $picture = uploadFile(
                    $request->file('image'),
                    Str::slug($employee->name).'_'.time(),
                    $this->permissionPath, true);
                $picture = url('storage'.$picture);

                return response()->json([
                    'message' => 'Success',
                    'data' => $picture
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                ]);
            }
        }else{
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    public function update(Request $request){
        if(Auth::check()){
            try {
                $urlStorage = url('storage');
                $image = Str::replace($urlStorage, '', $request->get('image'));
                $user = Auth::user();
                $user->update([
                    'name' => $request->get('name'),
                    'photo' => $image
                ]);
                $employee = Employee::find($user->employee_id);
                $employee->update([
                    'employee_number' => $request->get('employee_number'),
                ]);

                return response()->json([
                       'message' => 'Success',
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                ]);
            }
        }else{
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    public function changePassword(Request $request){
        if(Auth::check()){
            try {
                if(Hash::check($request->get('old_password'), Auth::user()->password)) {
                    $this->validate($request, [
                        'password' => 'required|confirmed|min:6',
                    ]);
                    $user = Auth::user();
                    $user->update([
                        'password' => Hash::make($request->get('password'))
                    ]);

                    return response()->json([
                        'message' => 'Success',
                    ]);
                }else{
                    return response()->json([
                        'message' => 'Failed, Password lama tidak sesuai',
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                ]);
            }
        }else{
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }
}
