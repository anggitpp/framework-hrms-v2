<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\PermissionRequest;
use App\Models\Attendance\AttendancePermission;
use App\Models\Employee\Employee;
use App\Models\Setting\AppMasterData;
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

class ESSPermissionController extends Controller
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
        $employee = Employee::find(Auth::user()->employee_id);
        $employee->position->location_id = AppMasterData::find($employee->position->location_id)->name ?? '';
        $employee->position->position_id = AppMasterData::find($employee->position->position_id)->name ?? '';
        $employee->position->grade_id = AppMasterData::find($employee->position->grade_id)->name ?? '';
        $employee->position->unit_id = AppMasterData::find($employee->position->unit_id)->name ?? '';

        return view('ess.permission.index', compact('employee'));
    }

    public function data(Request $request)
    {
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            $filterYear = $request->get('combo_1') ?? date('Y');
            return DataTables::of(
                DB::table('attendance_permissions as t1')
                    ->join('app_master_data as t2', 't1.category_id', 't2.id')
                    ->select(
                        't1.id',
                        't1.number',
                        't1.start_date',
                        't1.end_date',
                        't1.filename',
                        't1.approved_status',
                        't2.name as category_name')
                    ->whereYear('t1.start_date', $filterYear)
            )
                ->filter(function ($query) use ($filter) {
                    $query->where(function ($query) use ($filter) {
                        $query->where('t1.number', 'like', "%$filter%");
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
                        'url_edit' => route(str_replace('/', '.', $this->menu_path()).'.edit', $model->id),
                    ];
                    if($model->approved_status != 't'){
                        $arr = array_merge($arr, [
                            'url_destroy' => route(str_replace('/', '.', $this->menu_path()).'.destroy', $model->id)
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
        $employee = Employee::find(Auth::user()->employee_id);
        $employee->position->unit_id = AppMasterData::find($employee->position->unit_id)->name ?? '';
        $employee->position->rank_id = AppMasterData::find($employee->position->rank_id)->name ?? '';
        $getLastNumber = AttendancePermission::whereYear('date', date('Y'))->whereMonth('date', date('m'))
            ->orderBy('number', 'desc')
            ->pluck('number')
            ->first() ?? 0;
        //SET FORMAT FOR NUMBER LEAVE
        $lastNumber = Str::padLeft(intval(Str::substr($getLastNumber,0,4)) + 1, '4', '0').'/'.numToRoman(date('m')).'/IH/'.date('Y');

        return view('ess.permission.form', compact(['categories', 'employee', 'lastNumber']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PermissionRequest $request
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
        $employee = Employee::find(Auth::user()->employee_id);
        $employee->position->unit_id = AppMasterData::find($employee->position->unit_id)->name ?? '';
        $employee->position->rank_id = AppMasterData::find($employee->position->rank_id)->name ?? '';
        $isCanSave = !($permission->approved_status == 't');

        return view('ess.permission.form', compact(['categories', 'employee', 'permission', 'isCanSave']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PermissionRequest $request
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
}
