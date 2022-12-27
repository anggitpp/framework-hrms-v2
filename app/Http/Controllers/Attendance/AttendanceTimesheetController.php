<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\LeaveMasterRequest;
use App\Http\Requests\Attendance\LeaveRequest;
use App\Http\Requests\Attendance\PermissionRequest;
use App\Http\Requests\ESS\TimesheetRequest;
use App\Models\Attendance\AttendancePermission;
use App\Models\Employee\Employee;
use App\Models\ESS\EssTimesheet;
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

class AttendanceTimesheetController extends Controller
{
    public string $filePath;

    public function __construct()
    {
        $this->middleware('auth');
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
        \Session::put('user', $user);
        \Session::put('employees', $employees);

        return view('attendances.timesheet.index');
    }

    public function data(Request $request)
    {
        $user = \Session::get('user');
        if($request->ajax()){
            $filterMonth = $request->get('combo_1') ?? date('m');
            $filterYear = $request->get('combo_2') ?? date('Y');
            $filter = $request->get('search')['value'];

            $table = DB::table('ess_timesheets as t1')
                ->join('employees as t2', 't1.employee_id', 't2.id')
                ->join('employee_positions as t3', function ($join){
                    $join->on('t2.id', 't3.employee_id');
                    $join->where('t3.status', 't');
                })
                ->select(
                    't1.id',
                    't1.date',
                    't1.activity',
                    't1.output',
                    't1.start_time',
                    't1.end_time',
                    't1.duration',
                    't1.volume',
                    't1.type',
                    't2.name',
                    't2.employee_number')
                ->whereMonth('t1.date', $filterMonth)
                ->whereYear('t1.date', $filterYear);

            if(!$user->hasPermissionTo('lvl3 '.$this->menu_path()))
                $table->where('t3.leader_id', $user->employee_id);

            return DataTables::of($table)
                ->filter(function ($query) use ($filter, $filterMonth, $filterYear) {
                    $query->where(function ($query) use ($filter) {
                        $query->where('t2.name', 'like', "%$filter%")
                            ->orWhere('t2.employee_number', 'like', "%$filter%")
                            ->orWhere('t1.activity', 'like', "%$filter%")
                            ->orWhere('t1.output', 'like', "%$filter%");
                    });
                })
                ->editColumn('date', function ($model) {
                    return setDate($model->date);
                })
                ->addColumn('action', function ($model) {
                    $arr = [
                        'menu_path' => $this->menu_path(),
                        'isModal' => false,
                        'url_edit' => route(Str::replace('/', '.', $this->menu_path()).'.edit', $model->id),
                        'url_destroy' => route(Str::replace('/', '.', $this->menu_path()).'.destroy', $model->id),
                    ];
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
        $employees = \Session::get('employees');

        return view('attendances.timesheet.form', ['employees' => $employees]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TimesheetRequest $request
     * @return RedirectResponse
     */
    public function store(TimesheetRequest $request)
    {
        $duration = Carbon::parse($request->get('end_time'))->diff(Carbon::parse($request->get('start_time')))->format('%H:%I');

        EssTimesheet::create([
            'employee_id' => $request->get('employee_id'),
            'activity' => $request->get('activity'),
            'output' => $request->get('output'),
            'date' => $request->get('date') ? resetDate($request->get('date')) : null,
            'start_time' => $request->get('start_time'),
            'end_time' => $request->get('end_time'),
            'duration' => $duration,
            'volume' => $request->get('volume'),
            'type' => $request->get('type'),
            'description' => $request->get('description'),
        ]);

        Alert::success('Success', 'Data Kinerja berhasil disimpan');

        return redirect()->route(Str::replace('/', '.', $this->menu_path()).'.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $timesheet = EssTimesheet::with( 'employee.position')->find($id);
        $employees = \Session::get('employees');
        $timesheet->employee->position->unit_id = AppMasterData::find($timesheet->employee->position->unit_id)->name;
        $timesheet->employee->position->rank_id = AppMasterData::find($timesheet->employee->position->rank_id)->name;

        return view('attendances.timesheet.form', ['employees' => $employees, 'timesheet' => $timesheet]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TimesheetRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(TimesheetRequest $request, int $id)
    {
        $duration = Carbon::parse($request->get('end_time'))->diff(Carbon::parse($request->get('start_time')))->format('%H:%I');

        $timesheet = EssTimesheet::findOrFail($id);
        $timesheet->update([
            'employee_id' => $request->get('employee_id'),
            'activity' => $request->get('activity'),
            'output' => $request->get('output'),
            'date' => $request->get('date') ? resetDate($request->get('date')) : null,
            'start_time' => $request->get('start_time'),
            'end_time' => $request->get('end_time'),
            'duration' => $duration,
            'volume' => $request->get('volume'),
            'type' => $request->get('type'),
            'description' => $request->get('description'),
        ]);

        Alert::success('Success', 'Data Kinerja berhasil disimpan');

        return redirect()->route(Str::replace('/', '.', $this->menu_path()).'.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        $timesheet = EssTimesheet::findOrFail($id);
        $timesheet->delete();

        Alert::success('Success', 'Data Kinerja berhasil dihapus!');

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
}
