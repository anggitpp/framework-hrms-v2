<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\CorrectionRequest;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceCorrection;
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

class AttendanceCorrectionController extends Controller
{
    public string $filePath;
    public array $arrApprove;

    public function __construct()
    {
        $this->middleware('auth');
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

        return view('attendances.correction.index');
    }

    public function data(Request $request)
    {
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            $filterMonth = $request->get('combo_1') ?? date('m');
            $filterYear = $request->get('combo_2') ?? date('Y');
            $user = \Session::get('user');

            $table = DB::table('attendance_corrections as t1')
                ->join('employees as t2', 't1.employee_id', 't2.id')
                ->join('employee_positions as t3', function ($join){
                    $join->on('t2.id', 't3.employee_id');
                    $join->where('t3.status', 't');
                })
                ->select(
                    't1.id',
                    't1.number',
                    't1.attendance_date',
                    't1.start_time',
                    't1.end_time',
                    't1.approved_status',
                    't2.name',
                    't2.employee_number')
                ->whereMonth('t1.attendance_date', $filterMonth)
                ->whereYear('t1.attendance_date', $filterYear);
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
                ->editColumn('attendance_date', function ($model) {
                    return setDate($model->attendance_date);
                })
                ->editColumn('start_time', function ($model) {
                    return substr($model->start_time, 0, 5);
                })
                ->editColumn('end_time', function ($model) {
                    return substr($model->end_time, 0, 5);
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
        $employees = \Session::get('employees');
        $getLastNumber = AttendanceCorrection::whereYear('date', date('Y'))->whereMonth('date', date('m'))
            ->orderBy('number', 'desc')
            ->pluck('number')
            ->first() ?? 0;
        //SET FORMAT FOR NUMBER LEAVE
        $lastNumber = Str::padLeft(intval(Str::substr($getLastNumber,0,4)) + 1, '4', '0').'/'.numToRoman(date('m')).'/KA/'.date('Y');

        return view('attendances.correction.form', compact(['employees', 'lastNumber']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CorrectionRequest $request
     * @return RedirectResponse
     */
    public function store(CorrectionRequest $request)
    {
        $duration = Carbon::parse($request->get('start_time'))->diff(Carbon::parse($request->get('end_time')))->format('%H:%I');
        $actualDuration = Carbon::parse($request->get('actual_start_time'))->diff(Carbon::parse($request->get('actual_end_time')))->format('%H:%I');

        AttendanceCorrection::create([
            'number' => $request->input('number'),
            'employee_id' => $request->input('employee_id'),
            'date' => resetDate($request->input('date')),
            'attendance_date' => resetDate($request->input('attendance_date')),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'duration' => $duration,
            'actual_start_time' => $request->input('actual_start_time'),
            'actual_end_time' => $request->input('actual_end_time'),
            'actual_duration' => $actualDuration,
            'description' => $request->input('description'),
            'approved_status' => 'p'
        ]);

        Alert::success('Success', 'Data Koreksi berhasil disimpan');

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
        $correction = AttendanceCorrection::with( 'employee.position')->find($id);
        $employees = \Session::get('employees');
        $correction->employee->position->unit_id = AppMasterData::find($correction->employee->position->unit_id)->name;
        $correction->employee->position->rank_id = AppMasterData::find($correction->employee->position->rank_id)->name;

        return view('attendances.correction.form', compact(['correction', 'employees']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CorrectionRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(CorrectionRequest $request, int $id)
    {
        $correction = AttendanceCorrection::findOrFail($id);
        $duration = Carbon::parse($request->get('start_time'))->diff(Carbon::parse($request->get('end_time')))->format('%H:%I');
        $actualDuration = Carbon::parse($request->get('actual_start_time'))->diff(Carbon::parse($request->get('actual_end_time')))->format('%H:%I');

        $correction->update([
            'number' => $request->input('number'),
            'employee_id' => $request->input('employee_id'),
            'date' => resetDate($request->input('date')),
            'attendance_date' => resetDate($request->input('attendance_date')),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'duration' => $duration,
            'actual_start_time' => $request->input('actual_start_time'),
            'actual_end_time' => $request->input('actual_end_time'),
            'actual_duration' => $actualDuration,
            'description' => $request->input('description'),
            'approved_status' => 'p'
        ]);

        Alert::success('Success', 'Data Koreksi berhasil disimpan');

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
        $correction = AttendanceCorrection::findOrFail($id);
        $correction->delete();

        Alert::success('Success', 'Data Koreksi berhasil dihapus!');

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
        $data['data'] = AttendanceCorrection::findOrFail($id);
        $data['data']->approved_status = $data['data']->approved_status == 'p' ? 't' : $data['data']->approved_status;
        $data['actionRoute'] = route(str_replace('/', '.', $this->menu_path()).'.updateApprove', $id);
        $data['title'] = 'Approve Izin';

        return view('components.form.approve-form', $data);
    }

    public function updateApprove(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            $correction = AttendanceCorrection::findOrFail($id);
            $correction->update([
                'approved_status' => $request->input('approved_status'),
                'approved_by' => Auth::user()->id,
                'approved_at' => now()
            ]);

            if ($request->input('approved_status') == 't') {
                $attendance = Attendance::where('employee_id', $correction->employee_id)
                    ->whereDate('start_date', $correction->attendance_date)
                    ->first();

                $duration = Carbon::parse($correction->end_time)->diff(Carbon::parse($correction->start_time))->format('%H:%I');

                Attendance::updateOrCreate(
                    [
                        'employee_id' => $correction->employee_id,
                        'start_date' => $correction->attendance_date,
                    ],
                    [
                        'type' => $attendance->type ?? '1',
                        'type_id' => $correction->id,
                        'start_time' => $correction->start_time,
                        'end_time' => $correction->end_time,
                        'duration' => $duration,
                        'description' => $correction->description,
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success'=>'Data Koreksi berhasil disetujui',
                'url'=> route(str_replace('/', '.', $this->menu_path()).'.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success'=>'Data Koreksi gagal disetujui '.$e->getMessage(),
                'url'=> route(str_replace('/', '.', $this->menu_path()).'.index')
            ]);
        }
    }

    public function attendance(Request $request)
    {
        $attendance = Attendance::where('employee_id', $request->input('employee_id'))
            ->where('start_date', resetDate($request->input('date')))
            ->first();
        $attendance->start_time = substr($attendance->start_time, 0, 5);
        $attendance->end_time = substr($attendance->end_time, 0, 5);

        return $attendance;

    }
}
