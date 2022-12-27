<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\LeaveRequest;
use App\Models\Attendance\AttendanceLeave;
use App\Models\Attendance\AttendanceLeaveMaster;
use App\Models\Employee\Employee;
use App\Models\Setting\AppMasterData;
use Carbon\Carbon;
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

class ESSLeaveController extends Controller
{
    public string $filePath;
    public array $arrApprove;

    public function __construct()
    {
        $this->middleware('auth');
        $this->filePath = '/uploads/attendance/leave/';
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

        return view('ess.leave.index', compact('employee'));
    }

    public function data(Request $request)
    {
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            $filterYear = $request->get('combo_1') ?? date('Y');
            return DataTables::of(
                DB::table('attendance_leaves as t1')
                    ->join('attendance_leave_masters as t2', 't1.leave_master_id', 't2.id')
                    ->select(
                        't1.id',
                        't1.number',
                        't1.start_date',
                        't1.end_date',
                        't1.filename',
                        't1.approved_status',
                        't2.name as leave_name')
                ->whereYear('t1.start_date', $filterYear)
            )
                ->filter(function ($query) use ($filter) {
                    $query->where('t1.employee_id', Auth::user()->employee_id);
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
        $masters = AttendanceLeaveMaster::pluck('name', 'id')->toArray();
        $employee = Employee::find(Auth::user()->employee_id);
        $employee->position->unit_id = AppMasterData::find($employee->position->unit_id)->name ?? '';
        $employee->position->rank_id = AppMasterData::find($employee->position->rank_id)->name ?? '';
        $getLastNumber = AttendanceLeave::whereYear('created_at', date('Y'))
            ->orderBy('number', 'desc')
            ->pluck('number')
            ->first() ?? 0;
        //SET FORMAT FOR NUMBER LEAVE
        $lastNumber = Str::padLeft(intval(Str::substr($getLastNumber,0,4)) + 1, '4', '0').'/'.numToRoman(date('m')).'/IC/'.date('Y');

        return view('ess.leave.form', compact(['masters', 'employee', 'lastNumber']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LeaveRequest $request
     * @return RedirectResponse
     */
    public function store(LeaveRequest $request)
    {
        $file = uploadFile(
            $request->file('filename'),
            'leave_'.Str::slug($request->input('number')).'_'.time(),
            $this->filePath);

        AttendanceLeave::create([
            'number' => $request->input('number'),
            'employee_id' => $request->input('employee_id'),
            'leave_master_id' => $request->input('leave_master_id'),
            'date' => resetDate($request->input('date')),
            'start_date' => resetDate($request->input('start_date')),
            'end_date' => resetDate($request->input('end_date')),
            'filename' => $file,
            'balance' => $request->input('balance') ?? 0,
            'amount' => $request->input('amount') ?? 0,
            'remaining' => $request->input('remaining') ?? 0,
            'description' => $request->input('description'),
            'approved_status' => 'p'
        ]);

        Alert::success('Success', 'Data Cuti berhasil disimpan');

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
        $leave = AttendanceLeave::with( 'employee.position')->find($id);
        $masters = AttendanceLeaveMaster::pluck('name', 'id')->toArray();
        $employee = Employee::find(Auth::user()->employee_id);
        $employee->position->unit_id = AppMasterData::find($employee->position->unit_id)->name ?? '';
        $employee->position->rank_id = AppMasterData::find($employee->position->rank_id)->name ?? '';
        $isCanSave = !($leave->approved_status == 't');

        return view('ess.leave.form', compact(['masters', 'employee', 'leave', 'isCanSave']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LeaveRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(LeaveRequest $request, int $id)
    {
        $leave = AttendanceLeave::findOrFail($id);
        if($request->file('filename')){
            $file = uploadFile(
                $request->file('filename'),
                'leave_'.Str::slug($request->input('number')).'_'.time(),
                $this->filePath);
            $leave->filename = $file;
            $leave->save();
        }

        $leave->update([
            'number' => $request->input('number'),
            'employee_id' => $request->input('employee_id'),
            'leave_master_id' => $request->input('leave_master_id'),
            'date' => resetDate($request->input('date')),
            'start_date' => resetDate($request->input('start_date')),
            'end_date' => resetDate($request->input('end_date')),
            'balance' => $request->input('balance') ?? 0,
            'amount' => $request->input('amount') ?? 0,
            'remaining' => $request->input('remaining') ?? 0,
            'description' => $request->input('description'),
            'approved_status' => 'p'
        ]);

        Alert::success('Success', 'Data Cuti berhasil disimpan');

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
        $leave = AttendanceLeave::findOrFail($id);
        $leave->delete();

        Alert::success('Success', 'Data Cuti berhasil dihapus!');

        return redirect()->back();

    }

    public function leave(Request $request)
    {
        $master = AttendanceLeaveMaster::find($request->input('leave_master_id'));
        $amount = AttendanceLeave::where('employee_id', $request->input('employee_id'))
            ->where('leave_master_id', $request->input('leave_master_id'))
            ->where('approved_status', '!=', 'f')
            ->sum('amount');
        $employee = Employee::find($request->input('employee_id'));
        $master->isUnlimited = false;
        if($master->balance == 0) $master->isUnlimited = true;

        if($master->balance != 0) $master->balance = $master->balance - $amount;

        if($master->gender != "a") {
            if ($master->gender != $employee->gender){
                $master->balance = 0;
            }
        }

        return $master;
    }

    public function totalLeave(Request $request)
    {
        $startDate = resetDate($request->input('start_date'));
        $endDate = resetDate($request->input('end_date'));

        $data['amount'] = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $data['remaining'] = $request->get('balance') - $data['amount'];

        return $data;
    }
}
