<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeFamilyRequest;
use App\Http\Requests\Employee\EmployeeRequest;
use App\Models\Attendance\AttendanceShift;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeFamily;
use App\Models\Employee\EmployeePosition;
use App\Models\Setting\AppMasterData;
use Auth;
use Barryvdh\Debugbar\Facades\Debugbar;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
    public array $genderOption;

    public function __construct()
    {
        $this->middleware('auth');
        $this->photoPath = '/uploads/employee/photo/';
        $this->identityPath = '/uploads/employee/identity/';
        $this->familyPath = '/uploads/employee/family/';
        $this->genderOption = ['m' => "Laki-Laki", "f" => "Perempuan"];

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
        $masters = [];
        $dataMaster = AppMasterData::whereIn('app_master_category_code', ['ELK', 'EP', 'EG', 'ESPK', 'ESP', 'ETP', 'EMP', 'EMU'])
            ->where('status', 't')
            ->orderBy('app_master_category_code')
            ->orderBy('order')
            ->get();
        foreach ($dataMaster as $key => $value){
            $masters[$value->app_master_category_code][$value->id] = $value->name;
        }

        $user = Auth::user();

        if($request->ajax()){
            $filterPosition = $request->get('combo_1');
            $filterRank = $request->get('combo_2');
            $filterGrade = $request->get('combo_3');
            $filterLocation = $request->get('combo_4');
            $filter = $request->get('search')['value'];

            $table = DB::table('employees as t1')
                ->join('employee_positions as t2', function ($join) {
                    $join->on('t1.id', 't2.employee_id')
                        ->where('t2.status', 't');
                })
                ->select(['t1.id', 't1.employee_number', 't1.name', 't1.join_date', 't2.position_id', 't2.rank_id']);

            if(!$user->hasPermissionTo('lvl3 '.$this->menu_path()))
                $table->where('t2.leader_id', $user->employee_id);

            return DataTables::of($table)
                ->filter(function ($query) use ($filter, $filterPosition, $filterRank, $filterGrade, $filterLocation) {
                    if (isset($filter)) $query->where('name', 'like', "%{$filter}%")->orWhere('employee_number', 'like', "%{$filter}%");
                    if (isset($filterPosition)) $query->where('position_id', $filterPosition);
                    if (isset($filterRank)) $query->where('rank_id', $filterRank);
                    if (isset($filterGrade)) $query->where('grade_id', $filterGrade);
                    if (isset($filterLocation)) $query->where('location_id', $filterLocation);
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
            'masters' => $masters
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory
     */
    public function create()
    {
        $user = Session::get('user');

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

            return redirect()->route('employees.employees.index');

        }catch (Exception $e) {
            DB::rollBack();

            Alert::error('Error', $e->getMessage());

            return redirect()->route('employees.employees.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory
     */
    public function show(int $id)
    {
        $masters = AppMasterData::pluck('name', 'id')->toArray();

        $employee = Employee::find($id);
        $employee->position->location_id = AppMasterData::find($employee->position->location_id)->name ?? '';
        $employee->position->position_id = AppMasterData::find($employee->position->position_id)->name ?? '';
        $employee->position->grade_id = AppMasterData::find($employee->position->grade_id)->name ?? '';
        $employee->position->unit_id = AppMasterData::find($employee->position->unit_id)->name ?? '';

        $families = EmployeeFamily::where('employee_id', $id)->get();

        return view('employees.employee.show', [
            'employee' => $employee,
            'masters' => $masters,
            'families' => $families,
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

            return redirect()->route('employees.employees.index');

        } catch (Exception $e) {
            DB::rollBack();

            Alert::error('Error', $e->getMessage());

            return redirect()->route('employees.employees.index');
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
            if ($request->hasFile('filename')) {
                $resize = false;
                $extension = $request->file('filename')->extension();
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') $resize = true;
                Debugbar::info($extension);

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
}
