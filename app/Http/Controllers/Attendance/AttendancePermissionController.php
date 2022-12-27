<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\LeaveMasterRequest;
use App\Http\Requests\Attendance\LeaveRequest;
use App\Http\Requests\Attendance\PermissionRequest;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendancePermission;
use App\Models\Employee\Employee;
use App\Models\Setting\AppMasterData;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;

class AttendancePermissionController extends Controller
{
    public string $filePath;
    public array $arrApprove;

    public function __construct()
    {
        $this->middleware('auth');
        $this->filePath = '/uploads/attendance/permission/';
        $this->arrApprove = defaultStatusApproval();

        \View::share('arrApprove', $this->arrApprove);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $user = Auth::user();
        $employees = Employee::select(['id','name', 'employee_number', DB::raw("CONCAT(employee_number, ' - ', name) as empName")])
            ->whereHas('position', function ($query) use ($user) {
                if(!$user->hasPermissionTo('lvl3 '.$this->menu_path())) $query->where('leader_id', $user->employee_id);
            })
            ->pluck('empName', 'id')
            ->toArray();

        \Session::put('employees', $employees);
        \Session::put('user', $user);

        return view('attendances.permission.index');
    }

    public function data(Request $request)
    {
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            $filterMonth = $request->get('combo_1') ?? date('m');
            $filterYear = $request->get('combo_2') ?? date('Y');
            $user = \Session::get('user');

            $table = DB::table('attendance_permissions as t1')
                ->join('employees as t2', 't1.employee_id', 't2.id')
                ->join('employee_positions as t3', function ($join){
                    $join->on('t2.id', 't3.employee_id');
                    $join->where('t3.status', 't');
                })
                ->join('app_master_data as t4', 't1.category_id', 't4.id')
                ->select(
                    't1.id',
                    't1.number',
                    't1.start_date',
                    't1.end_date',
                    't1.filename',
                    't1.approved_status',
                    't2.name',
                    't2.employee_number',
                    't4.name as category_name')
                ->whereMonth('t1.start_date', $filterMonth)
                ->whereYear('t1.start_date', $filterYear);
            if(!$user->hasPermissionTo('lvl3 '.$this->menu_path()))
                $table->where('t3.leader_id', $user->employee_id);

            return DataTables::of($table)
                ->filter(function ($query) use ($filter) {
                    $query->where(function ($query) use ($filter) {
                        $query->where('t2.name', 'like', "%$filter%")
                            ->orWhere('t2.employee_number', 'like', "%$filter%")
                            ->orWhere('t1.number', 'like', "%$filter%");
                    });
                })
                ->editColumn('filename', function ($model) {
                    return $model->filename ? view('components.datatables.download', [
                        'url' => $model->filename
                    ]) : '';
                })
                ->editColumn('start_date', function ($model) {
                    return setDate($model->start_date);
                })
                ->editColumn('end_date', function ($model) {
                    return setDate($model->end_date);
                })
                ->editColumn('approved_status', function ($model) {
                    return view('components.views.approve-status', [
                        'status' => $model->approved_status,
                    ]);
                })
                ->addColumn('action', function ($model) {
                    $arr = [
                        'menu_path' => $this->menu_path(),
                        'isModal' => false,
                    ];
                    if($model->approved_status != 't'){
                        $arr = array_merge($arr, [
                            'url_edit' => route(Str::replace('/', '.', $this->menu_path()).'.edit', $model->id),
                            'url_destroy' => route(Str::replace('/', '.', $this->menu_path()).'.destroy', $model->id),
                            'url_slot' => route(Str::replace('/', '.', $this->menu_path()).'.approve', $model->id),
                            'icon_slot' => 'fa-solid fa-check',
                        ]);
                    }
                    return view('components.views.action', $arr);
                })
                ->addIndexColumn()
                ->make();
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $categories = AppMasterData::whereAppMasterCategoryCode('AKI')->pluck('name', 'id')->toArray();
        $employees = \Session::get('employees');

        $getLastNumber = AttendancePermission::whereYear('date', date('Y'))->whereMonth('date', date('m'))
            ->orderBy('number', 'desc')
            ->pluck('number')
            ->first() ?? 0;
        //SET FORMAT FOR NUMBER LEAVE
        $lastNumber = Str::padLeft(intval(Str::substr($getLastNumber,0,4)) + 1, '4', '0').'/'.numToRoman(date('m')).'/IH/'.date('Y');

        return view('attendances.permission.form', compact(['categories', 'employees', 'lastNumber']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LeaveRequest $request
     * @return RedirectResponse
     */
    public function store(PermissionRequest $request)
    {
        $file = uploadFile(
            $request->file('filename'),
            'permission_'.Str::slug($request->input('number')).'_'.time(),
            $this->filePath);

        AttendancePermission::create([
            'number' => $request->input('number'),
            'employee_id' => $request->input('employee_id'),
            'category_id' => $request->input('category_id'),
            'date' => resetDate($request->input('date')),
            'start_date' => resetDate($request->input('start_date')),
            'end_date' => resetDate($request->input('end_date')),
            'filename' => $file,
            'description' => $request->input('description'),
            'approved_status' => 'p'
        ]);

        Alert::success('Success', 'Data Izin berhasil disimpan');

        return redirect()->route(str_replace('/', '.', $this->menu_path()).'.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $permission = AttendancePermission::with( 'employee.position')->find($id);
        $categories = AppMasterData::whereAppMasterCategoryCode('AKI')->pluck('name', 'id')->toArray();
        $employees = \Session::get('employees');

        $permission->employee->position->unit_id = AppMasterData::find($permission->employee->position->unit_id)->name;
        $permission->employee->position->rank_id = AppMasterData::find($permission->employee->position->rank_id)->name;

        return view('attendances.permission.form', compact(['categories', 'employees', 'permission']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LeaveMasterRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(PermissionRequest $request, int $id)
    {
        $permission = AttendancePermission::findOrFail($id);
        if($request->file('filename')){
            $file = uploadFile(
                $request->file('filename'),
                'permission_'.Str::slug($request->input('number')).'_'.time(),
                $this->filePath);
            $permission->filename = $file;
            $permission->save();
        }

        $permission->update([
            'number' => $request->input('number'),
            'employee_id' => $request->input('employee_id'),
            'category_id' => $request->input('category_id'),
            'date' => resetDate($request->input('date')),
            'start_date' => resetDate($request->input('start_date')),
            'end_date' => resetDate($request->input('end_date')),
            'description' => $request->input('description'),
            'approved_status' => 'p'
        ]);

        Alert::success('Success', 'Data Izin berhasil disimpan');

        return redirect()->route(str_replace('/', '.', $this->menu_path()).'.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        $permission = AttendancePermission::findOrFail($id);
        $permission->delete();

        Alert::success('Success', 'Data Izin berhasil dihapus!');

        return redirect()->back();

    }

    public function employee(Request $request)
    {
        //GET DATA EMPLOYEE
        $employee = Employee::with('position')
            ->where('id', $request->input('employee_id'))
            ->first();

        //OVERIDE RANK FROM MASTER DATA
        $employee->position->rank_id = AppMasterData::where('id', $employee->position->rank_id)->first()->name;
        //OVERIDE UNIT FROM MASTER DATA
        $employee->position->unit_id = AppMasterData::where('id', $employee->position->unit_id)->first()->name;

        return $employee;
    }

    public function approve(int $id)
    {
        $data['data'] = AttendancePermission::findOrFail($id);
        $data['data']->approved_status = $data['data']->approved_status == 'p' ? 't' : $data['data']->approved_status;
        $data['actionRoute'] = route(str_replace('/', '.', $this->menu_path()).'.updateApprove', $id);
        $data['title'] = 'Approve Izin';

        return view('components.form.approve-form', $data);
    }

    public function updateApprove(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            $permission = AttendancePermission::findOrFail($id);
            $permission->update([
                'approved_status' => $request->input('approved_status'),
                'approved_by' => Auth::user()->id,
                'approved_date' => resetDate($request->input('approved_date')),
                'approved_note' => $request->input('approved_note')
            ]);

            if($permission->approved_status == 't'){
                $period = CarbonPeriod::create($permission->start_date, $permission->end_date);

                foreach ($period as $date) {
                    $formattedDate = $date->format('Y-m-d');

                    Attendance::updateOrCreate([
                        'employee_id' => $permission->employee_id,
                        'start_date' => $formattedDate,
                    ],[
                        'end_date' => $formattedDate,
                        'type' => 'I',
                        'type_id' => $permission->id,
                        'description' => $permission->description,
                    ]);
                }
            }else{
                $attendances = Attendance::where('type_id', $permission->id)->exists();
                if($attendances){
                    Attendance::where('type_id', $permission->id)->delete();
                }
            }

            DB::commit();

            return response()->json([
                'success'=>'Data Izin berhasil disetujui',
                'url'=> route(str_replace('/', '.', $this->menu_path()).'.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error'=>'Error: '. $e->getMessage(),
                'url'=> route(str_replace('/', '.', $this->menu_path()).'.index')
            ]);
        }
    }
}
