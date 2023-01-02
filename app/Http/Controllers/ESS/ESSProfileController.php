<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\ESS\ProfileRequest;
use App\Models\Employee\Employee;
use App\Models\Setting\AppMasterData;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class ESSProfileController extends Controller
{
    public string $photoPath;
    public string $identityPath;
    public array $genderOption;

    public function __construct()
    {
        $this->middleware('auth');
        $this->photoPath = '/uploads/employee/photo/';
        $this->identityPath = '/uploads/employee/identity/';
        $this->genderOption = ['m' => "Laki-Laki", "f" => "Perempuan"];

        \View::share('genderOption', $this->genderOption);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $masters = AppMasterData::pluck('name', 'id')->toArray();

        $employee = Employee::find(Auth::user()->employee_id);
        $employee->position->location_id = AppMasterData::find($employee->position->location_id)->name ?? '';
        $employee->position->position_id = AppMasterData::find($employee->position->position_id)->name ?? '';
        $employee->position->grade_id = AppMasterData::find($employee->position->grade_id)->name ?? '';
        $employee->position->unit_id = AppMasterData::find($employee->position->unit_id)->name ?? '';

        return view('ess.profile.index', [
            'employee' => $employee,
            'masters' => $masters,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Application|Factory|View
     */
    public function edit()
    {
        $employee = Employee::find(Auth::user()->employee_id);
        $maritals = AppMasterData::where('app_master_category_code', 'ESPK')->pluck('name', 'id')->toArray();
        $statuses = AppMasterData::where('app_master_category_code', 'ESP')->pluck('name', 'id')->toArray();
        $religions = AppMasterData::where('app_master_category_code', 'EAG')->pluck('name', 'id')->toArray();

        return view('ess.profile.form', [
            'employee' => $employee,
            'maritals' => $maritals,
            'statuses' => $statuses,
            'religions' => $religions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProfileRequest $request
     * @return RedirectResponse
     */
    public function update(ProfileRequest $request)
    {
        $employee = Employee::findOrFail(Auth::user()->employee_id);

        $photo = $employee->photo;
        $identity_file = $employee->identity_file;
        if($request->hasFile('photo')) $photo = uploadFile($request->file('photo'), 'photo_'.Str::slug($request->input('name')).'_'.time(), $this->photoPath, true);
        if($request->hasFile('identity_file')) $identity_file = uploadFile($request->file('identity_file'), 'identity_file_'.Str::slug($request->input('name')).'_'.time(), $this->identityPath, true);

        $employee->update([
            'name' => $request->input('name'),
            'nickname' => $request->input('nickname'),
            'date_of_birth' => $request->input('date_of_birth') ? resetDate($request->input('date_of_birth')) : null,
            'place_of_birth' => $request->input('place_of_birth'),
            'employee_number' => $request->input('employee_number'),
            'identity_number' => $request->input('identity_number'),
            'identity_address' => $request->input('identity_address'),
            'address' => $request->input('address'),
            'status_id' => $request->input('status_id'),
            'photo' => $photo,
            'identity_file' => $identity_file,
            'join_date' => $request->input('join_date') ? resetDate($request->input('join_date')) : null,
            'leave_date' => $request->input('leave_date') ? resetDate($request->input('leave_date')) : null,
            'phone_number' => $request->input('phone_number'),
            'mobile_phone_number' => $request->input('mobile_phone_number'),
            'marital_status_id' => $request->input('marital_status_id'),
            'email' => $request->input('email'),
            'gender' => $request->input('gender'),
            'attendance_pin' => $request->input('attendance_pin'),
            'religion_id' => $request->input('religion_id'),
        ]);

        Alert::success('Success', 'Profile berhasil diubah');

        return redirect()->route(Str::replace('/', '.', $this->menu_path()).'.index');
    }
}
