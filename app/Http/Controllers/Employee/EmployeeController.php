<?php

namespace App\Http\Controllers\Employee;

use App\Exports\Employee\EmployeeExport;
use App\Exports\GlobalExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeAssetRequest;
use App\Http\Requests\Employee\EmployeeContactRequest;
use App\Http\Requests\Employee\EmployeeFileRequest;
use App\Http\Requests\Employee\EmployeePositionHistoryRequest;
use App\Http\Requests\Employee\EmployeeFamilyRequest;
use App\Http\Requests\Employee\EmployeeRequest;
use App\Http\Requests\Employee\EmployeeTrainingRequest;
use App\Http\Requests\Employee\EmployeeWorkRequest;
use App\Imports\Employee\EmployeeImport;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceCorrection;
use App\Models\Attendance\AttendanceLeave;
use App\Models\Attendance\AttendanceOvertime;
use App\Models\Attendance\AttendancePermission;
use App\Models\Attendance\AttendanceShift;
use App\Models\Attendance\AttendanceWorkSchedule;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeAsset;
use App\Models\Employee\EmployeeContact;
use App\Models\Employee\EmployeeEducation;
use App\Models\Employee\EmployeeFamily;
use App\Models\Employee\EmployeeFile;
use App\Models\Employee\EmployeePosition;
use App\Models\Employee\EmployeeTraining;
use App\Models\Employee\EmployeeWork;
use App\Models\ESS\EssTimesheet;
use App\Models\Setting\AppMasterData;
use App\Models\Setting\AppParameter;
use Auth;
use Barryvdh\Debugbar\Facades\Debugbar;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Session;
use Storage;
use View;
use Yajra\DataTables\DataTables;

ini_set('memory_limit', '4096M');

class EmployeeController extends Controller
{

    public string $photoPath;
    public string $identityPath;
    public string $familyPath;
    public string $educationPath;
    public string $trainingPath;
    public string $workPath;
    public string $assetPath;
    public string $filePath;
    public string $leavePath;
    public string $overtimePath;
    public string $permissionPath;
    public string $logPath;
    public array $genderOption;

    public function __construct()
    {
        $this->middleware('auth');
        $this->photoPath = '/uploads/employee/photo/';
        $this->identityPath = '/uploads/employee/identity/';
        $this->familyPath = '/uploads/employee/family/';
        $this->educationPath = '/uploads/employee/education/';
        $this->trainingPath = '/uploads/employee/training/';
        $this->workPath = '/uploads/employee/work/';
        $this->assetPath = '/uploads/employee/asset/';
        $this->filePath = '/uploads/employee/file/';
        $this->leavePath = '/uploads/attendance/leave/';
        $this->overtimePath = '/uploads/attendance/overtime/';
        $this->permissionPath = '/uploads/attendance/permission/';
        $this->logPath = '/log/';
        $this->genderOption = ['m' => "Laki-Laki", "f" => "Perempuan"];


        View::share('statusOption', defaultStatus());
        View::share('genderOption', $this->genderOption);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Application|Factory|\Illuminate\Contracts\View\View|JsonResponse
     * @throws Exception
     */
    public function index(Request $request): Factory|\Illuminate\Contracts\View\View|JsonResponse|Application
    {
        $file = public_path()."/storage/log/contact-import 2023-01-10.log";


        $this->downloadFile();

        $masters = [];
        $dataMaster = AppMasterData::whereIn('app_master_category_code', ['ELK', 'EP', 'EG', 'ESPK', 'ESP', 'ETP', 'EMP', 'EMU'])
            ->where('status', 't')
            ->orderBy('app_master_category_code')
            ->orderBy('order')
            ->get();
        foreach ($dataMaster as $key => $value){
            $masters[$value->app_master_category_code][$value->id] = $value->name;
        }

        $getStatusActive = AppParameter::whereCode('SAP')->first()->value;

        $statusNonActives = AppMasterData::where('app_master_category_code', 'ESP')
            ->whereNot('id', $getStatusActive)
            ->pluck('name', 'id')
            ->toArray();

        $user = Auth::user();

        if($request->ajax()){
            $filterPosition = $request->get('combo_1');
            $filterRank = $request->get('combo_2');
            $filterGrade = $request->get('combo_3');
            $filterLocation = $request->get('combo_4');
            $filterStatus = $request->get('combo_5');
            $filter = $request->get('search')['value'];

            $table = DB::table('employees as t1')
                ->join('employee_positions as t2', function ($join) {
                    $join->on('t1.id', 't2.employee_id')
                        ->where('t2.status', 't');
                })
                ->select(['t1.id', 't1.employee_number', 't1.name', 't1.join_date', 't1.leave_date', 't1.status_id', 't2.position_id', 't2.rank_id']);
                if(str_contains($this->menu_path(), 'nonactive')) {
                    $table->whereNot('t1.status_id', $getStatusActive);
                }else{
                    $table->where('t1.status_id', $getStatusActive);
                }

            if(!$user->hasPermissionTo('lvl3 '.$this->menu_path()))
                $table->where('t2.leader_id', $user->employee_id);

            return DataTables::of($table)
                ->filter(function ($query) use ($filter, $filterPosition, $filterRank, $filterGrade, $filterLocation, $filterStatus) {
                    if (isset($filter)) $query->where('name', 'like', "%{$filter}%")->orWhere('employee_number', 'like', "%{$filter}%");
                    if (isset($filterPosition)) $query->where('position_id', $filterPosition);
                    if (isset($filterRank)) $query->where('rank_id', $filterRank);
                    if (isset($filterGrade)) $query->where('grade_id', $filterGrade);
                    if (isset($filterLocation)) $query->where('location_id', $filterLocation);
                    if (isset($filterStatus)) $query->where('status_id', $filterStatus);
                })
                ->editColumn('position_id', function ($model) use ($masters) {
                    return $masters['EMP'][$model->position_id] ?? '';
                })
                ->editColumn('rank_id', function ($model) use ($masters) {
                    return $masters['EP'][$model->rank_id] ?? '';
                })
                ->editColumn('join_date', function ($model) {
                    return $model->join_date ? setDate($model->join_date) : '';
                })
                ->editColumn('leave_date', function ($model) {
                    return $model->leave_date ? setDate($model->leave_date) : '';
                })
                ->editColumn('status_id', function ($model) use ($masters) {
                    return $masters['ESP'][$model->status_id] ?? '';
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route(str_replace('/', '.', $this->menu_path()).'.edit', $model->id),
                        'url_destroy' => route(str_replace('/', '.', $this->menu_path()).'.destroy', $model->id),
                        'url_slot' => route(str_replace('/', '.', $this->menu_path()).'.show', $model->id),
                        'isModalSlot' => false,
                        'isModal' => false
                    ]);
                })
                ->addIndexColumn()
                ->make();
        }

        Session::put('user', $user);

        return view('employees.employee.index', [
            'masters' => $masters,
            'statusNonActives' => $statusNonActives,
        ]);
    }

    public function downloadFile() {
        $path = storage_path('app/public/log/contact-import 2023-01-10.log');

        return response()->download($path);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory
     */
    public function create()
    {
        $user = Session::get('user');

        $masters = AppMasterData::whereIn('app_master_category_code', ['ELK', 'EP', 'EG', 'ESPK', 'ESP', 'ETP', 'EMP', 'EMU', 'EAG'])->get();

        $data['maritals'] = $masters->where('app_master_category_code', 'ESPK')->pluck('name', 'id')->toArray();
        $data['religions'] = $masters->where('app_master_category_code', 'EAG')->pluck('name', 'id')->toArray();
        $data['statuses'] = $masters->where('app_master_category_code', 'ESP')->pluck('name', 'id')->toArray();
        $data['types'] = $masters->where('app_master_category_code', 'ETP')->pluck('name', 'id')->toArray();
        $data['locations'] = $masters->where('app_master_category_code', 'ELK')->pluck('name', 'id')->toArray();
        $data['ranks'] = $masters->where('app_master_category_code', 'EP')->pluck('name', 'id')->toArray();
        $data['grades'] = $masters->where('app_master_category_code', 'EG')->pluck('name', 'id')->toArray();
        $data['units'] = $masters->where('app_master_category_code', 'EMU')->pluck('name', 'id')->toArray();
        $data['positions'] = $masters->where('app_master_category_code', 'EMP')->pluck('name', 'id')->toArray();
        $data['shifts'] = AttendanceShift::pluck('name', 'id')->toArray();
        $data['employees'] = Employee::select(['id','name', 'employee_number', DB::raw("CONCAT(employee_number, ' - ', name) as empName")])
            ->whereHas('position', function ($query) use ($user) {
                if(!$user->hasPermissionTo('lvl3 '.$this->menu_path())) $query->where('leader_id', $user->employee_id);
            })
            ->orderBy('name')
            ->pluck('empName', 'id')
            ->toArray();

        return view('employees.employee.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeeRequest $request
     * @return RedirectResponse
     */
    public function store(EmployeeRequest $request)
    {
        DB::beginTransaction();

        $photo = uploadFile(
            $request->file('photo'),
            'photo_'.Str::slug($request->input('name')).'_'.time(),
            $this->photoPath, true);

        $identity_file = uploadFile(
            $request->file('identity_file'),
            'identity_file_'.Str::slug($request->input('name')).'_'.time(),
            $this->identityPath, true);

        try {
            $employee = Employee::create([
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
                'religion_id' => $request->input('religion_id'),
                'email' => $request->input('email'),
                'gender' => $request->input('gender'),
                'attendance_pin' => $request->input('attendance_pin'),
            ]);

            EmployeePosition::create([
                'employee_id' => $employee->id,
                'position_id' => $request->input('position_id'),
                'start_date' => $request->input('start_date') ? resetDate($request->input('start_date')) : null,
                'end_date' => $request->input('end_date') ? resetDate($request->input('end_date')) : null,
                'rank_id' => $request->input('rank_id'),
                'grade_id' => $request->input('grade_id'),
                'location_id' => $request->input('location_id'),
                'shift_id' => $request->input('shift_id'),
                'employee_type_id' => $request->input('employee_type_id'),
                'sk_date' => $request->input('sk_date') ? resetDate($request->input('sk_date')) : null,
                'sk_number' => $request->input('sk_number'),
                'unit_id' => $request->input('unit_id'),
                'leader_id' => $request->input('leader_id'),
                'status' => 't',
            ]);

            DB::commit();

            Alert::success('Success', 'Data Pegawai berhasil disimpan');


            return redirect()->route(Str::replace('/', '.', $this->menu_path()).'.index');

        }catch (Exception $e) {
            DB::rollBack();

            Alert::error('Error', $e->getMessage());

            return redirect()->route(Str::replace('/', '.', $this->menu_path()).'.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory
     */
    public function show(Request $request, int $id)
    {
        $masters = AppMasterData::pluck('name', 'id')->toArray();

        $employee = Employee::find($id);
        $employee->position->location_id = AppMasterData::find($employee->position->location_id)->name ?? '';
        $employee->position->position_id = AppMasterData::find($employee->position->position_id)->name ?? '';
        $employee->position->grade_id = AppMasterData::find($employee->position->grade_id)->name ?? '';
        $employee->position->unit_id = AppMasterData::find($employee->position->unit_id)->name ?? '';

        $families = EmployeeFamily::whereEmployeeId($id)->paginate($this->defaultPagination($request));
        $educations = EmployeeEducation::whereEmployeeId($id)->paginate($this->defaultPagination($request));
        $contacts = EmployeeContact::whereEmployeeId($id)->paginate($this->defaultPagination($request));
        $positions = EmployeePosition::whereEmployeeId($id)->paginate($this->defaultPagination($request));
        $trainings = EmployeeTraining::whereEmployeeId($id)->paginate($this->defaultPagination($request));
        $works = EmployeeWork::whereEmployeeId($id)->paginate($this->defaultPagination($request));
        $assets = EmployeeAsset::whereEmployeeId($id)->paginate($this->defaultPagination($request));
        $files = EmployeeFile::whereEmployeeId($id)->paginate($this->defaultPagination($request));

        return view('employees.employee.show', [
            'employee' => $employee,
            'masters' => $masters,
            'families' => $families,
            'educations' => $educations,
            'contacts' => $contacts,
            'positions' => $positions,
            'trainings' => $trainings,
            'works' => $works,
            'assets' => $assets,
            'files' => $files,
            'menu_path' => $this->menu_path(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory
     */
    public function edit(int $id)
    {
        $user = Session::get('user');

        $masters = AppMasterData::whereIn('app_master_category_code', ['ELK', 'EP', 'EG', 'ESPK', 'ESP', 'ETP', 'EMP', 'EMU', 'EAG'])->get();

        $data['maritals'] = $masters->where('app_master_category_code', 'ESPK')->pluck('name', 'id')->toArray();
        $data['religions'] = $masters->where('app_master_category_code', 'EAG')->pluck('name', 'id')->toArray();
        $data['statuses'] = $masters->where('app_master_category_code', 'ESP')->pluck('name', 'id')->toArray();
        $data['types'] = $masters->where('app_master_category_code', 'ETP')->pluck('name', 'id')->toArray();
        $data['locations'] = $masters->where('app_master_category_code', 'ELK')->pluck('name', 'id')->toArray();
        $data['ranks'] = $masters->where('app_master_category_code', 'EP')->pluck('name', 'id')->toArray();
        $data['grades'] = $masters->where('app_master_category_code', 'EG')->pluck('name', 'id')->toArray();
        $data['units'] = $masters->where('app_master_category_code', 'EMU')->pluck('name', 'id')->toArray();
        $data['positions'] = $masters->where('app_master_category_code', 'EMP')->pluck('name', 'id')->toArray();
        $data['shifts'] = AttendanceShift::pluck('name', 'id')->toArray();
        $data['employees'] = Employee::select(['id','name', 'employee_number', DB::raw("CONCAT(employee_number, ' - ', name) as empName")])
            ->whereHas('position', function ($query) use ($user) {
                if(!$user->hasPermissionTo('lvl3 '.$this->menu_path())) $query->where('leader_id', $user->employee_id);
            })
            ->orderBy('name')
            ->pluck('empName', 'id')
            ->toArray();

        $data['employee'] = Employee::with('position')->findOrFail($id);

        return view('employees.employee.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeeRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(EmployeeRequest $request, int $id)
    {
        DB::beginTransaction();

        $employee = Employee::findOrFail($id);

        $photo = $employee->photo;
        $identity_file = $employee->identity_file;
        if($request->hasFile('photo')) $photo = uploadFile($request->file('photo'), 'photo_'.Str::slug($request->input('name')).'_'.time(), $this->photoPath, true);
        if($request->hasFile('identity_file')) $identity_file = uploadFile($request->file('identity_file'), 'identity_file_'.Str::slug($request->input('name')).'_'.time(), $this->identityPath, true);

        try {
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
                'religion_id' => $request->input('religion_id'),
                'email' => $request->input('email'),
                'gender' => $request->input('gender'),
                'attendance_pin' => $request->input('attendance_pin'),
            ]);

            $employee->position->update([
                'employee_id' => $employee->id,
                'position_id' => $request->input('position_id'),
                'start_date' => $request->input('start_date') ? resetDate($request->input('start_date')) : null,
                'end_date' => $request->input('end_date') ? resetDate($request->input('end_date')) : null,
                'rank_id' => $request->input('rank_id'),
                'grade_id' => $request->input('grade_id'),
                'location_id' => $request->input('location_id'),
                'shift_id' => $request->input('shift_id'),
                'employee_type_id' => $request->input('employee_type_id'),
                'sk_date' => $request->input('sk_date') ? resetDate($request->input('sk_date')) : null,
                'sk_number' => $request->input('sk_number'),
                'unit_id' => $request->input('unit_id'),
                'leader_id' => $request->input('leader_id'),
            ]);

            DB::commit();

            Alert::success('Success', 'Data Pegawai berhasil diubah');

            return redirect()->route(Str::replace('/', '.', $this->menu_path()).'.index');

        } catch (Exception $e) {
            DB::rollBack();

            Alert::error('Error', $e->getMessage());

            return redirect()->route(Str::replace('/', '.', $this->menu_path()).'.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            $employee = Employee::findOrFail($id);

            $employee->positions()->delete();
            $employee->delete();

            $family = EmployeeFamily::where('employee_id', $id)->get();
            foreach ($family as $item) {
                if(Storage::exists($this->familyPath.$item->photo)) Storage::delete($this->familyPath.$item->photo);
                $item->delete();
            }

            $contact = EmployeeContact::where('employee_id', $id)->get();
            foreach ($contact as $item) {
                $item->delete();
            }

            $education = EmployeeEducation::where('employee_id', $id)->get();
            foreach ($education as $item) {
                if(Storage::exists($this->educationPath.$item->filename)) Storage::delete($this->educationPath.$item->filename);
                $item->delete();
            }

            $training = EmployeeTraining::where('employee_id', $id)->get();
            foreach ($training as $item) {
                if(Storage::exists($this->trainingPath.$item->filename)) Storage::delete($this->trainingPath.$item->filename);
                $item->delete();
            }

            $work = EmployeeWork::where('employee_id', $id)->get();
            foreach ($work as $item) {
                if(Storage::exists($this->workPath.$item->filename)) Storage::delete($this->workPath.$item->filename);
                $item->delete();
            }

            $asset = EmployeeAsset::where('employee_id', $id)->get();
            foreach ($asset as $item) {
                if(Storage::exists($this->assetPath.$item->filename)) Storage::delete($this->assetPath.$item->filename);
                $item->delete();
            }

            $file = EmployeeFile::where('employee_id', $id)->get();
            foreach ($file as $item) {
                if(Storage::exists($this->filePath.$item->filename)) Storage::delete($this->filePath.$item->filename);
                $item->delete();
            }

            $attendance = Attendance::where('employee_id', $id)->get();
            foreach ($attendance as $item) {
                $item->delete();
            }

            $leave = AttendanceLeave::whereEmployeeId($id)->get();
            foreach ($leave as $item) {
                if(Storage::exists($this->leavePath.$item->filename)) Storage::delete($this->leavePath.$item->filename);
                $item->delete();
            }

            $overtime = AttendanceOvertime::whereEmployeeId($id)->get();
            foreach ($overtime as $item) {
                if(Storage::exists($this->overtimePath.$item->filename)) Storage::delete($this->overtimePath.$item->filename);
                $item->delete();
            }

            $permission = AttendancePermission::whereEmployeeId($id)->get();
            foreach ($permission as $item) {
                if(Storage::exists($this->permissionPath.$item->filename)) Storage::delete($this->permissionPath.$item->filename);
                $item->delete();
            }

            $correction = AttendanceCorrection::whereEmployeeId($id)->get();
            foreach ($correction as $item) {
                $item->delete();
            }

            $timesheet = EssTimesheet::whereEmployeeId($id)->get();
            foreach ($timesheet as $item) {
                $item->delete();
            }

            DB::commit();

            Alert::success('Success', 'Data Pegawai berhasil dihapus');

            return redirect()->back();

        } catch (Exception $e) {
            DB::rollBack();

            Alert::error('Error', $e->getMessage());

            return redirect()->back();
        }
    }

    public function familyCreate(int $id)
    {
        $relationships = AppMasterData::whereAppMasterCategoryCode('EHK')->pluck('name', 'id')->toArray();

        return view('employees.employee.family-form', [
            'id' => $id,
            'relationships' => $relationships,
        ]);
    }
    public function familyStore(EmployeeFamilyRequest $request, int $id)
    {
        try {
            $filename = '';
            if($request->hasFile('filename')) {
                $resize = false;
                $extension = $request->file('filename')->extension();
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') $resize = true;

                $filename = uploadFile(
                    $request->file('filename'),
                    'employee-family_' . Str::slug($request->input('name')) . '_' . time(),
                    $this->familyPath, $resize);
            }

            EmployeeFamily::create([
                'employee_id' => $request->input('employee_id'),
                'name' => $request->input('name'),
                'relationship_id' => $request->input('relationship_id'),
                'identity_number' => $request->input('identity_number'),
                'gender' => $request->input('gender'),
                'birth_date' => $request->input('birth_date') ? resetDate($request->input('birth_date')) : null,
                'birth_place' => $request->input('birth_place'),
                'description' => $request->input('description'),
                'filename' => $filename,
            ]);

            return response()->json([
                'success'=>'Data Keluarga berhasil disimpan',
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.show', $id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success'=>'Gagal '.$e->getMessage(),
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.show', $id),
            ]);
        }
    }

    public function familyEdit(int $id)
    {
        $relationships = AppMasterData::whereAppMasterCategoryCode('EHK')->pluck('name', 'id')->toArray();
        $family = EmployeeFamily::findOrFail($id);

        return view('employees.employee.family-form', [
            'id' => $id,
            'relationships' => $relationships,
            'family' => $family,
        ]);
    }

    public function familyUpdate(EmployeeFamilyRequest $request, int $id)
    {
        $family = EmployeeFamily::find($id);

        try {
            if($request->get('isDelete') == 't'){
                deleteFile($this->familyPath.$family->filename);
                $family->update([
                    'filename' => null,
                ]);
            }
            if ($request->hasFile('filename')) {
                $resize = false;
                $extension = $request->file('filename')->extension();
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') $resize = true;

                $filename = uploadFile(
                    $request->file('filename'),
                    'employee-family_' . Str::slug($request->input('name')) . '_' . time(),
                    $this->familyPath, $resize);

                $family->update([
                    'filename' => $filename,
                ]);
            }

            $family->update([
                'employee_id' => $request->input('employee_id'),
                'name' => $request->input('name'),
                'relationship_id' => $request->input('relationship_id'),
                'identity_number' => $request->input('identity_number'),
                'gender' => $request->input('gender'),
                'birth_date' => $request->input('birth_date') ? resetDate($request->input('birth_date')) : null,
                'birth_place' => $request->input('birth_place'),
                'description' => $request->input('description'),
            ]);

            return response()->json([
                'success' => 'Data Keluarga berhasil disimpan',
                'url' => route(Str::replace('/', '.', $this->menu_path()) . '.show', $family->employee_id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => 'Gagal ' . $e->getMessage(),
                'url' => route(Str::replace('/', '.', $this->menu_path()) . '.show', $family->employee_id),
            ]);
        }
    }

    public function familyDestroy(int $id)
    {
        $family = EmployeeFamily::findOrFail($id);
        try {
            if(Storage::exists($this->familyPath.$family->filename)) Storage::delete($this->familyPath.$family->filename);
            $family->delete();

            Alert::success('Success', 'Data Keluarga berhasil dihapus');

            return redirect()->back();
        } catch (Exception $e) {
            Alert::success('Success', 'Gagal ' . $e->getMessage());

            return redirect()->back();
        }
    }

    public function educationCreate(int $id)
    {
        $levels = AppMasterData::whereAppMasterCategoryCode('EMJP')->pluck('name', 'id')->toArray();

        return view('employees.employee.education-form', [
            'id' => $id,
            'levels' => $levels,
        ]);
    }

    public function educationStore(EmployeePositionHistoryRequest $request, int $id)
    {
        $employee = Employee::findOrFail($id);
        try {
            $filename = '';
            if($request->hasFile('filename')){
                $resize = false;
                $extension = $request->file('filename')->extension();
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') $resize = true;

                $filename = uploadFile(
                    $request->file('filename'),
                    'employee-education_' . Str::slug($employee->name) . '_' . time(),
                    $this->educationPath, $resize);
            }

            EmployeeEducation::create([
                'employee_id' => $request->input('employee_id'),
                'level_id' => $request->input('level_id'),
                'name' => $request->input('name'),
                'major' => $request->input('major'),
                'start_year' => $request->input('start_year'),
                'end_year' => $request->input('end_year'),
                'score' => $request->input('score'),
                'city' => $request->input('city'),
                'filename' => $filename,
                'description' => $request->input('description'),
            ]);

            return response()->json([
                'success'=>'Data Pendidikan berhasil disimpan',
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.show', $id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success'=>'Gagal '.$e->getMessage(),
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.show', $id),
            ]);
        }
    }

    public function educationEdit(int $id)
    {
        $levels = AppMasterData::whereAppMasterCategoryCode('EMJP')->pluck('name', 'id')->toArray();
        $education = EmployeeEducation::find($id);

        return view('employees.employee.education-form', [
            'id' => $id,
            'levels' => $levels,
            'education' => $education,
        ]);
    }

    public function educationUpdate(EmployeePositionHistoryRequest $request, int $id)
    {
        $education = EmployeeEducation::find($id);

        try {
            if($request->get('isDelete') == 't'){
                deleteFile($this->educationPath.$education->filename);
                $education->update([
                    'filename' => null,
                ]);
            }
            if ($request->hasFile('filename')) {
                $resize = false;
                $extension = $request->file('filename')->extension();
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') $resize = true;

                $filename = uploadFile(
                    $request->file('filename'),
                    'employee-education_' . Str::slug($education->employee->name) . '_' . time(),
                    $this->educationPath, $resize);

                $education->update([
                    'filename' => $filename,
                ]);
            }

            $education->update([
                'employee_id' => $request->input('employee_id'),
                'level_id' => $request->input('level_id'),
                'name' => $request->input('name'),
                'major' => $request->input('major'),
                'start_year' => $request->input('start_year'),
                'end_year' => $request->input('end_year'),
                'score' => $request->input('score'),
                'city' => $request->input('city'),
                'description' => $request->input('description'),
            ]);

            return response()->json([
                'success' => 'Data Pendidikan berhasil disimpan',
                'url' => route(Str::replace('/', '.', $this->menu_path()) . '.show', $education->employee_id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => 'Gagal ' . $e->getMessage(),
                'url' => route(Str::replace('/', '.', $this->menu_path()) . '.show', $education->employee_id),
            ]);
        }
    }

    public function educationDestroy(int $id)
    {
        $education = EmployeeEducation::findOrFail($id);
        try {
            if(Storage::exists($this->educationPath.$education->filename)) Storage::delete($this->educationPath.$education->filename);
            $education->delete();

            Alert::success('Success', 'Data Pendidikan berhasil dihapus');

            return redirect()->back();
        } catch (Exception $e) {
            Alert::success('Success', 'Gagal ' . $e->getMessage());

            return redirect()->back();
        }
    }

    public function contactCreate(int $id)
    {
        $relationships = AppMasterData::whereAppMasterCategoryCode('EHK')->pluck('name', 'id')->toArray();

        return view('employees.employee.contact-form', [
            'id' => $id,
            'relationships' => $relationships,
        ]);
    }
    public function contactStore(EmployeeContactRequest $request, int $id)
    {
        try {
            EmployeeContact::create([
                'employee_id' => $request->input('employee_id'),
                'name' => $request->input('name'),
                'relationship_id' => $request->input('relationship_id'),
                'phone_number' => $request->input('phone_number'),
                'description' => $request->input('description'),
            ]);

            return response()->json([
                'success'=>'Data Kontak berhasil disimpan',
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.show', $id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success'=>'Gagal '.$e->getMessage(),
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.show', $id),
            ]);
        }
    }

    public function contactEdit(int $id)
    {
        $relationships = AppMasterData::whereAppMasterCategoryCode('EHK')->pluck('name', 'id')->toArray();
        $contact = EmployeeContact::findOrFail($id);

        return view('employees.employee.contact-form', [
            'id' => $id,
            'relationships' => $relationships,
            'contact' => $contact,
        ]);
    }

    public function contactUpdate(EmployeeContactRequest $request, int $id)
    {
        $contact = EmployeeContact::find($id);

        try {
            $contact->update([
                'employee_id' => $request->input('employee_id'),
                'name' => $request->input('name'),
                'relationship_id' => $request->input('relationship_id'),
                'phone_number' => $request->input('phone_number'),
                'description' => $request->input('description'),
            ]);

            return response()->json([
                'success' => 'Data Kontak berhasil disimpan',
                'url' => route(Str::replace('/', '.', $this->menu_path()) . '.show', $contact->employee_id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => 'Gagal ' . $e->getMessage(),
                'url' => route(Str::replace('/', '.', $this->menu_path()) . '.show', $contact->employee_id),
            ]);
        }
    }

    public function contactDestroy(int $id)
    {
        $contact = EmployeeContact::findOrFail($id);
        try {
            $contact->delete();

            Alert::success('Success', 'Data Kontak berhasil dihapus');

            return redirect()->back();
        } catch (Exception $e) {
            Alert::success('Success', 'Gagal ' . $e->getMessage());

            return redirect()->back();
        }
    }

    public function positionCreate(int $id)
    {
        $user = Session::get('user');

        $masters = AppMasterData::whereIn('app_master_category_code', ['ELK', 'EP', 'EG', 'ESPK', 'ESP', 'ETP', 'EMP', 'EMU'])->get();

        $data['id'] = $id;
        $data['maritals'] = $masters->where('app_master_category_code', 'ESPK')->pluck('name', 'id')->toArray();
        $data['statuses'] = $masters->where('app_master_category_code', 'ESP')->pluck('name', 'id')->toArray();
        $data['types'] = $masters->where('app_master_category_code', 'ETP')->pluck('name', 'id')->toArray();
        $data['locations'] = $masters->where('app_master_category_code', 'ELK')->pluck('name', 'id')->toArray();
        $data['ranks'] = $masters->where('app_master_category_code', 'EP')->pluck('name', 'id')->toArray();
        $data['grades'] = $masters->where('app_master_category_code', 'EG')->pluck('name', 'id')->toArray();
        $data['units'] = $masters->where('app_master_category_code', 'EMU')->pluck('name', 'id')->toArray();
        $data['positions'] = $masters->where('app_master_category_code', 'EMP')->pluck('name', 'id')->toArray();
        $data['shifts'] = AttendanceShift::pluck('name', 'id')->toArray();
        $data['employees'] = Employee::select(['id','name', 'employee_number', DB::raw("CONCAT(employee_number, ' - ', name) as empName")])
            ->whereHas('position', function ($query) use ($user) {
                if(!$user->hasPermissionTo('lvl3 '.$this->menu_path())) $query->where('leader_id', $user->employee_id);
            })
            ->orderBy('name')
            ->pluck('empName', 'id')
            ->toArray();

        return view('employees.employee.position-form', $data);
    }

    public function positionStore(EmployeePositionHistoryRequest $request, int $id)
    {
        DB::beginTransaction();

        try {
            $request->merge(['sk_date' => $request->input('sk_date') ? resetDate($request->input('sk_date')) : '']);
            $request->merge(['start_date' => $request->input('start_date') ? resetDate($request->input('start_date')) : '']);
            $request->merge(['end_date' => $request->input('end_date') ? resetDate($request->input('end_date')) : '']);

            $position = EmployeePosition::create($request->all());

            if($request->input('status') == 't') EmployeePosition::where('employee_id', $request->input('employee_id'))->where('id', '!=', $position->id)->update(['status' => 'f']);

            DB::commit();

            return response()->json([
                'success'=>'Data Jabatan berhasil disimpan',
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.show', $id),
            ]);
        } catch (Exception $e) {

            DB::rollBack();

            return response()->json([
                'success'=>'Gagal '.$e->getMessage(),
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.show', $id),
            ]);
        }
    }

    public function positionEdit(int $id)
    {
        $user = Session::get('user');

        $data['position'] = EmployeePosition::find($id);
        $data['id'] = $id;

        $masters = AppMasterData::whereIn('app_master_category_code', ['ELK', 'EP', 'EG', 'ESPK', 'ESP', 'ETP', 'EMP', 'EMU'])->get();

        $data['maritals'] = $masters->where('app_master_category_code', 'ESPK')->pluck('name', 'id')->toArray();
        $data['statuses'] = $masters->where('app_master_category_code', 'ESP')->pluck('name', 'id')->toArray();
        $data['types'] = $masters->where('app_master_category_code', 'ETP')->pluck('name', 'id')->toArray();
        $data['locations'] = $masters->where('app_master_category_code', 'ELK')->pluck('name', 'id')->toArray();
        $data['ranks'] = $masters->where('app_master_category_code', 'EP')->pluck('name', 'id')->toArray();
        $data['grades'] = $masters->where('app_master_category_code', 'EG')->pluck('name', 'id')->toArray();
        $data['units'] = $masters->where('app_master_category_code', 'EMU')->pluck('name', 'id')->toArray();
        $data['positions'] = $masters->where('app_master_category_code', 'EMP')->pluck('name', 'id')->toArray();
        $data['shifts'] = AttendanceShift::pluck('name', 'id')->toArray();
        $data['employees'] = Employee::select(['id','name', 'employee_number', DB::raw("CONCAT(employee_number, ' - ', name) as empName")])
            ->whereHas('position', function ($query) use ($user) {
                if(!$user->hasPermissionTo('lvl3 '.$this->menu_path())) $query->where('leader_id', $user->employee_id);
            })
            ->orderBy('name')
            ->pluck('empName', 'id')
            ->toArray();

        return view('employees.employee.position-form', $data);
    }

    public function positionUpdate(EmployeePositionHistoryRequest $request, int $id)
    {
        DB::beginTransaction();

        $position = EmployeePosition::findOrFail($id);

        try {
            $request->merge(['sk_date' => $request->input('sk_date') ? resetDate($request->input('sk_date')) : '']);
            $request->merge(['start_date' => $request->input('start_date') ? resetDate($request->input('start_date')) : '']);
            $request->merge(['end_date' => $request->input('end_date') ? resetDate($request->input('end_date')) : '']);

            $position->update($request->all());

            if($request->input('status') == 't') EmployeePosition::where('employee_id', $request->input('employee_id'))->where('id', '!=', $position->id)->update(['status' => 'f']);

            DB::commit();

            return response()->json([
                'success' => 'Data Jabatan berhasil disimpan',
                'url' => route(Str::replace('/', '.', $this->menu_path()) . '.show', $position->employee_id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => 'Gagal ' . $e->getMessage(),
                'url' => route(Str::replace('/', '.', $this->menu_path()) . '.show', $position->employee_id),
            ]);
        }
    }

    public function positionDestroy(int $id)
    {
        $position = EmployeePosition::findOrFail($id);
        try {
            $position->delete();

            Alert::success('Success', 'Data Jabatan berhasil dihapus');

            return redirect()->back();
        } catch (Exception $e) {
            Alert::success('Success', 'Gagal ' . $e->getMessage());

            return redirect()->back();
        }
    }

    public function trainingCreate(int $id)
    {
        $categories = AppMasterData::whereAppMasterCategoryCode('EKPL')->pluck('name', 'id')->toArray();
        $types = AppMasterData::whereAppMasterCategoryCode('ETPL')->pluck('name', 'id')->toArray();

        return view('employees.employee.training-form', [
            'id' => $id,
            'categories' => $categories,
            'types' => $types,
        ]);
    }

    public function trainingStore(EmployeeTrainingRequest $request, int $id)
    {
        $employee = Employee::findOrFail($id);
        try {
            $filename = '';
            if($request->hasFile('filename')){
                $resize = false;
                $extension = $request->file('filename')->extension();
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') $resize = true;

                $filename = uploadFile(
                    $request->file('filename'),
                    'employee-training' . Str::slug($employee->name) . '_' . time(),
                    $this->trainingPath, $resize);
            }

            $request->merge(['start_date' => resetDate($request->input('start_date'))]);
            $request->merge(['end_date' => resetDate($request->input('end_date'))]);

            $training = EmployeeTraining::create($request->except('filename'));
            $training->filename = $filename;
            $training->save();

            return response()->json([
                'success'=>'Data Pelatihan berhasil disimpan',
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.show', $id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success'=>'Gagal '.$e->getMessage(),
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.show', $id),
            ]);
        }
    }

    public function trainingEdit(int $id)
    {
        $categories = AppMasterData::whereAppMasterCategoryCode('EKPL')->pluck('name', 'id')->toArray();
        $types = AppMasterData::whereAppMasterCategoryCode('ETPL')->pluck('name', 'id')->toArray();
        $training = EmployeeTraining::find($id);

        return view('employees.employee.training-form', [
            'id' => $id,
            'training' => $training,
            'categories' => $categories,
            'types' => $types,
        ]);
    }

    public function trainingUpdate(EmployeeTrainingRequest $request, int $id)
    {
        $training = EmployeeTraining::find($id);

        try {
            if($request->get('isDelete') == 't'){
                deleteFile($this->trainingPath.$training->filename);
                $training->update([
                    'filename' => null,
                ]);
            }
            if ($request->hasFile('filename')) {
                $resize = false;
                $extension = $request->file('filename')->extension();
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') $resize = true;

                $filename = uploadFile(
                    $request->file('filename'),
                    'employee-training_' . Str::slug($training->employee->name) . '_' . time(),
                    $this->trainingPath, $resize);

                $training->update([
                    'filename' => $filename,
                ]);
            }

            $request->merge(['start_date' => resetDate($request->input('start_date'))]);
            $request->merge(['end_date' => resetDate($request->input('end_date'))]);

            $training->update($request->except('filename'));

            return response()->json([
                'success' => 'Data Pelatihan berhasil disimpan',
                'url' => route(Str::replace('/', '.', $this->menu_path()) . '.show', $training->employee_id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => 'Gagal ' . $e->getMessage(),
                'url' => route(Str::replace('/', '.', $this->menu_path()) . '.show', $training->employee_id),
            ]);
        }
    }

    public function trainingDestroy(int $id)
    {
        $training = EmployeeTraining::findOrFail($id);
        try {
            if(Storage::exists($this->trainingPath.$training->filename)) Storage::delete($this->trainingPath.$training->filename);
            $training->delete();

            Alert::success('Success', 'Data Pelatihan berhasil dihapus');

            return redirect()->back();
        } catch (Exception $e) {
            Alert::success('Success', 'Gagal ' . $e->getMessage());

            return redirect()->back();
        }
    }

    public function workCreate(int $id)
    {
        return view('employees.employee.work-form', compact('id'));
    }

    public function workStore(EmployeeWorkRequest $request, int $id)
    {
        $employee = Employee::find($id);
        try {
            $filename = '';
            if($request->hasFile('filename')){
                $resize = false;
                $extension = $request->file('filename')->extension();
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') $resize = true;

                $filename = uploadFile(
                    $request->file('filename'),
                    'employee-work' . Str::slug($employee->name) . '_' . time(),
                    $this->workPath, $resize);
            }

            $request->merge(['start_date' => resetDate($request->input('start_date'))]);
            $request->merge(['end_date' => $request->input('end_date') ? resetDate($request->input('end_date')) : '']);

            $work = EmployeeWork::create($request->except('filename'));
            $work->filename = $filename;
            $work->save();

            return response()->json([
                'success'=>'Data Kerja berhasil disimpan',
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.show', $id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success'=>'Gagal '.$e->getMessage(),
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.show', $id),
            ]);
        }
    }

    public function workEdit(int $id)
    {
        $work = EmployeeWork::find($id);

        return view('employees.employee.work-form', [
            'id' => $id,
            'work' => $work,
        ]);
    }

    public function workUpdate(EmployeeWorkRequest $request, int $id)
    {
        $work = EmployeeWork::find($id);

        try {
            if($request->get('isDelete') == 't'){
                deleteFile($this->workPath.$work->filename);
                $work->update([
                    'filename' => null,
                ]);
            }
            if ($request->hasFile('filename')) {
                $resize = false;
                $extension = $request->file('filename')->extension();
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') $resize = true;

                $filename = uploadFile(
                    $request->file('filename'),
                    'employee-work_' . Str::slug($work->employee->name) . '_' . time(),
                    $this->workPath, $resize);

                $work->update([
                    'filename' => $filename,
                ]);
            }

            $request->merge(['start_date' => resetDate($request->input('start_date'))]);
            $request->merge(['end_date' => $request->input('end_date') ? resetDate($request->input('end_date')) : '']);

            $work->update($request->except('filename'));

            return response()->json([
                'success' => 'Data Kerja berhasil disimpan',
                'url' => route(Str::replace('/', '.', $this->menu_path()) . '.show', $work->employee_id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => 'Gagal ' . $e->getMessage(),
                'url' => route(Str::replace('/', '.', $this->menu_path()) . '.show', $work->employee_id),
            ]);
        }
    }

    public function workDestroy(int $id)
    {
        $work = EmployeeWork::findOrFail($id);
        try {
            if(Storage::exists($this->workPath.$work->filename)) Storage::delete($this->workPath.$work->filename);
            $work->delete();

            Alert::success('Success', 'Data Kerja berhasil dihapus');

            return redirect()->back();
        } catch (Exception $e) {
            Alert::success('Success', 'Gagal ' . $e->getMessage());

            return redirect()->back();
        }
    }

    public function assetCreate(int $id)
    {
        $categories = AppMasterData::whereAppMasterCategoryCode('EKAS')->pluck('name', 'id')->toArray();
        $types = AppMasterData::whereAppMasterCategoryCode('ETAS')->pluck('name', 'id')->toArray();

        return view('employees.employee.asset-form', [
            'id' => $id,
            'categories' => $categories,
            'types' => $types,
        ]);
    }

    public function assetStore(EmployeeAssetRequest $request, int $id)
    {
        $employee = Employee::find($id);
        try {
            $filename = '';
            if($request->hasFile('filename')){
                $resize = false;
                $extension = $request->file('filename')->extension();
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') $resize = true;

                $filename = uploadFile(
                    $request->file('filename'),
                    'employee-asset' . Str::slug($employee->name) . '_' . time(),
                    $this->assetPath, $resize);
            }

            $request->merge(['date' => resetDate($request->input('date'))]);
            $request->merge(['start_date' => $request->input('start_date') ? resetDate($request->input('start_date')) : '']);
            $request->merge(['end_date' => $request->input('end_date') ? resetDate($request->input('end_date')) : '']);

            $asset = EmployeeAsset::create($request->except('filename'));
            $asset->filename = $filename;
            $asset->save();

            return response()->json([
                'success'=>'Data Asset berhasil disimpan',
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.show', $id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success'=>'Gagal '.$e->getMessage(),
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.show', $id),
            ]);
        }
    }

    public function assetEdit(int $id)
    {
        $asset = EmployeeAsset::find($id);
        $categories = AppMasterData::whereAppMasterCategoryCode('EKAS')->pluck('name', 'id')->toArray();
        $types = AppMasterData::whereAppMasterCategoryCode('ETAS')->pluck('name', 'id')->toArray();

        return view('employees.employee.asset-form', [
            'id' => $id,
            'asset' => $asset,
            'categories' => $categories,
            'types' => $types,
        ]);
    }

    public function assetUpdate(EmployeeAssetRequest $request, int $id)
    {
        $asset = EmployeeAsset::find($id);

        try {
            if($request->get('isDelete') == 't'){
                deleteFile($this->assetPath.$asset->filename);
                $asset->update([
                    'filename' => null,
                ]);
            }
            if ($request->hasFile('filename')) {
                $resize = false;
                $extension = $request->file('filename')->extension();
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') $resize = true;

                $filename = uploadFile(
                    $request->file('filename'),
                    'employee-asset_' . Str::slug($asset->employee->name) . '_' . time(),
                    $this->assetPath, $resize);

                $asset->update([
                    'filename' => $filename,
                ]);
            }

            $request->merge(['date' => resetDate($request->input('date'))]);
            $request->merge(['start_date' => $request->input('start_date') ? resetDate($request->input('start_date')) : '']);
            $request->merge(['end_date' => $request->input('end_date') ? resetDate($request->input('end_date')) : '']);

            $asset->update($request->except('filename'));

            return response()->json([
                'success' => 'Data Asset berhasil disimpan',
                'url' => route(Str::replace('/', '.', $this->menu_path()) . '.show', $asset->employee_id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => 'Gagal ' . $e->getMessage(),
                'url' => route(Str::replace('/', '.', $this->menu_path()) . '.show', $asset->employee_id),
            ]);
        }
    }

    public function assetDestroy(int $id)
    {
        $asset = EmployeeAsset::findOrFail($id);
        try {
            if(Storage::exists($this->assetPath.$asset->filename)) Storage::delete($this->assetPath.$asset->filename);
            $asset->delete();

            Alert::success('Success', 'Data Asset berhasil dihapus');

            return redirect()->back();
        } catch (Exception $e) {
            Alert::success('Success', 'Gagal ' . $e->getMessage());

            return redirect()->back();
        }
    }

    public function fileCreate(int $id)
    {
        return view('employees.employee.file-form', compact('id'));
    }
    public function fileStore(EmployeeFileRequest $request, int $id)
    {
        $employee = Employee::find($id);
        try {
            $filename = '';
            if($request->hasFile('filename')){
                $resize = false;
                $extension = $request->file('filename')->extension();
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') $resize = true;

                $filename = uploadFile(
                    $request->file('filename'),
                    'employee-file' . Str::slug($employee->name) . '_' . time(),
                    $this->filePath, $resize);
            }

            $asset = EmployeeFile::create($request->except('filename'));
            $asset->filename = $filename;
            $asset->save();

            return response()->json([
                'success'=>'Data File berhasil disimpan',
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.show', $id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success'=>'Gagal '.$e->getMessage(),
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.show', $id),
            ]);
        }
    }

    public function fileEdit(int $id)
    {
        $file = EmployeeFile::findOrFail($id);

        return view('employees.employee.file-form', [
            'id' => $id,
            'file' => $file,
        ]);
    }

    public function fileUpdate(EmployeeFileRequest $request, int $id)
    {
        $file = EmployeeFile::find($id);

        try {
            if($request->get('isDelete') == 't'){
                deleteFile($this->filePath.$file->filename);
                $file->update([
                    'filename' => null,
                ]);
            }
            if ($request->hasFile('filename')) {
                $resize = false;
                $extension = $request->file('filename')->extension();
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') $resize = true;

                $filename = uploadFile(
                    $request->file('filename'),
                    'employee-file_' . Str::slug($file->employee->name) . '_' . time(),
                    $this->filePath, $resize);

                $file->update([
                    'filename' => $filename,
                ]);
            }

            $file->update($request->except('filename'));

            return response()->json([
                'success' => 'Data File berhasil disimpan',
                'url' => route(Str::replace('/', '.', $this->menu_path()) . '.show', $file->employee_id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => 'Gagal ' . $e->getMessage(),
                'url' => route(Str::replace('/', '.', $this->menu_path()) . '.show', $file->employee_id),
            ]);
        }
    }

    public function fileDestroy(int $id)
    {
        $file = EmployeeFile::findOrFail($id);
        try {
            if(Storage::exists($this->filePath.$file->filename)) Storage::delete($this->filePath.$file->filename);
            $file->delete();

            Alert::success('Success', 'Data File berhasil dihapus');

            return redirect()->back();
        } catch (Exception $e) {
            Alert::success('Success', 'Gagal ' . $e->getMessage());

            return redirect()->back();
        }
    }

    public function export(Request $request)
    {
        $masters = AppMasterData::whereIn('app_master_category_code', ['ELK', 'EP', 'EG', 'ESPK', 'ESP', 'ETP', 'EMP', 'EMU'])
            ->where('status', 't')
            ->pluck('name', 'id')
            ->toArray();

        $shifts = AttendanceShift::active()->pluck('name', 'id')->toArray();

        $emp = Employee::active()->pluck('name', 'id')->toArray();

        $getStatusActive = AppParameter::whereCode('SAP')->first()->value;

        $user = Auth::user();

        $filterPosition = $request->get('combo_1');
        $filterRank = $request->get('combo_2');
        $filterGrade = $request->get('combo_3');
        $filterLocation = $request->get('combo_4');
        $filterStatus = $request->get('combo_5');
        $filter = $request->get('filter');

        $sql = DB::table('employees as t1')
            ->join('employee_positions as t2', function ($join) {
                $join->on('t1.id', 't2.employee_id')
                    ->where('t2.status', 't');
            });
        if(str_contains($this->menu_path(), 'nonactive')) {
            $sql->whereNot('t1.status_id', $getStatusActive);
        }else{
            $sql->where('t1.status_id', $getStatusActive);
        }

        if(!$user->hasPermissionTo('lvl3 '.$this->menu_path())) $sql->where('t2.leader_id', $user->employee_id);

        if (isset($filter)) $sql->where('name', 'like', "%{$filter}%")->orWhere('employee_number', 'like', "%{$filter}%");
        if (isset($filterPosition)) $sql->where('position_id', $filterPosition);
        if (isset($filterRank)) $sql->where('rank_id', $filterRank);
        if (isset($filterGrade)) $sql->where('grade_id', $filterGrade);
        if (isset($filterLocation)) $sql->where('location_id', $filterLocation);
        if (isset($filterStatus) && $filterStatus != 'undefined') $sql->where('status_id', $filterStatus);

        $data = [];
        $employees = $sql->get();
        foreach ($employees as $k => $employee){
            $data[] = [
                $k + 1,
                $employee->name,
                $employee->nickname,
                $employee->place_of_birth,
                $employee->date_of_birth ? setDate($employee->date_of_birth) : '',
                $employee->employee_number." ",
                $employee->identity_number." ",
                $employee->gender == 'm' ? 'Laki-laki' : 'Perempuan',
                $employee->email,
                $employee->identity_address,
                $employee->address,
                $employee->mobile_phone_number." ",
                $employee->phone_number." ",
                $masters[$employee->position_id] ?? '',
                $masters[$employee->employee_type_id] ?? '',
                $masters[$employee->rank_id] ?? '',
                $masters[$employee->grade_id] ?? '',
                $employee->sk_number,
                $employee->sk_date ? setDate($employee->sk_date) : '',
                $employee->start_date ? setDate($employee->start_date) : '',
                $employee->end_date ? setDate($employee->end_date) : '',
                $masters[$employee->location_id] ?? '',
                $masters[$employee->unit_id] ?? '',
                $shifts[$employee->shift_id] ?? '',
                $emp[$employee->leader_id] ?? '',
                $masters[$employee->status_id] ?? '',
                $employee->join_date ? setDate($employee->join_date) : '',
                $employee->leave_date ? setDate($employee->leave_date) : '',
                $employee->attendance_pin,
                $masters[$employee->marital_status_id] ?? '',
                $masters[$employee->religion_id] ?? '',
            ];
        }

        $columns = ["no", "identitas" => array("nama", "nama panggilan", "tempat lahir", "tanggal lahir", "nomor pegawai", "nomor identitas", "jenis kelamin", "email",
                    "alamat ktp", "alamat domisili", "nomor handphone", "nomor telepon"), "posisi" => array("jabatan", "tipe pegawai", "pangkat", "grade", "nomor sk",
                    "tanggal sk", "tanggal mulai", "tanggal selesai", "lokasi kerja", "unit", "shift", "atasan langsung"), "status" => array("status", "tanggal masuk",
                    "tanggal keluar", "pin absen", "status kawin", "agama")];

        $widths = [10, 30, 20, 20, 20, 25, 20, 20, 20];

        $aligns = ['center', 'left', 'right', 'left'];

        return Excel::download(new GlobalExport(
            [
                'startColumn' => 'A',
                'startRow' => 5,
                'columns' => $columns,
                'widths' => $widths,
                'aligns' => $aligns,
                'data' => $data,
                'title' => 'Data Pegawai',
            ]
        ), 'Data Pegawai.xlsx');
    }

    public function import()
    {
        return view('components.form.import-form', [
            'menu_path' => $this->menu_path(),
            'title' => 'Import Data Pegawai',
        ]);
    }

    public function processImport(Request $request)
    {
        try {
            if($request->hasFile('filename')) {
                Excel::import(new EmployeeImport, $request->file('filename'));

                return response()->json([
                    'success' => 'Data Pegawai selesai diimport',
                    'url' => route(Str::replace('/', '.', $this->menu_path()) . '.index'),
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => 'Gagal ' . $e->getMessage(),
                'url' => route(Str::replace('/', '.', $this->menu_path()) . '.index'),
            ]);
        }
    }
}
