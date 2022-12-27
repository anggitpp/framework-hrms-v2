<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\CorrectionRequest;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceCorrection;
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

class ESSCorrectionController extends Controller
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
        $employee = Employee::find(Auth::user()->employee_id);
        $employee->position->location_id = AppMasterData::find($employee->position->location_id)->name ?? '';
        $employee->position->position_id = AppMasterData::find($employee->position->position_id)->name ?? '';
        $employee->position->grade_id = AppMasterData::find($employee->position->grade_id)->name ?? '';
        $employee->position->unit_id = AppMasterData::find($employee->position->unit_id)->name ?? '';


        return view('ess.correction.index', compact('employee'));
    }

    public function data(Request $request)
    {
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            $filterYear = $request->get('combo_1') ?? date('Y');
            return DataTables::of(
                DB::table('attendance_corrections')
                    ->select(
                        'id',
                        'number',
                        'attendance_date',
                        'start_time',
                        'end_time',
                        'approved_status')
                    ->whereYear('attendance_date', $filterYear)
            )
                ->filter(function ($query) use ($filter) {
                    $query->where(function ($query) use ($filter) {
                        $query->where('number', 'like', "%$filter%");
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
        $employee = Employee::find(Auth::user()->employee_id);
        $employee->position->unit_id = AppMasterData::find($employee->position->unit_id)->name ?? '';
        $employee->position->rank_id = AppMasterData::find($employee->position->rank_id)->name ?? '';

        $getLastNumber = AttendanceCorrection::whereYear('date', date('Y'))->whereMonth('date', date('m'))
            ->orderBy('number', 'desc')
            ->pluck('number')
            ->first() ?? 0;
        //SET FORMAT FOR NUMBER LEAVE
        $lastNumber = Str::padLeft(intval(Str::substr($getLastNumber,0,4)) + 1, '4', '0').'/'.numToRoman(date('m')).'/KA/'.date('Y');

        return view('ess.correction.form', compact(['employee', 'lastNumber']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CorrectionRequest $request
     * @return RedirectResponse
     */
    public function store(CorrectionRequest $request)
    {
        AttendanceCorrection::create([
            'number' => $request->input('number'),
            'employee_id' => $request->input('employee_id'),
            'date' => resetDate($request->input('date')),
            'attendance_date' => resetDate($request->input('attendance_date')),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'actual_start_time' => $request->input('actual_start_time'),
            'actual_end_time' => $request->input('actual_end_time'),
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
        $employee = Employee::find(Auth::user()->employee_id);
        $employee->position->unit_id = AppMasterData::find($employee->position->unit_id)->name ?? '';
        $employee->position->rank_id = AppMasterData::find($employee->position->rank_id)->name ?? '';
        $isCanSave = !($correction->approved_status == 't');

        return view('ess.correction.form', compact(['correction', 'employee', 'isCanSave']));
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

        $correction->update([
            'number' => $request->input('number'),
            'employee_id' => $request->input('employee_id'),
            'date' => resetDate($request->input('date')),
            'attendance_date' => resetDate($request->input('attendance_date')),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'actual_start_time' => $request->input('actual_start_time'),
            'actual_end_time' => $request->input('actual_end_time'),
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
