<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\OvertimeRequest;
use App\Models\Attendance\AttendanceOvertime;
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

class ESSOvertimeController extends Controller
{
    public string $filePath;
    public array $arrApprove;

    public function __construct()
    {
        $this->middleware('auth');
        $this->filePath = '/uploads/attendance/overtime/';
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

        return view('ess.overtime.index', compact('employee'));
    }

    public function data(Request $request)
    {
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            $filterYear = $request->get('combo_1') ?? date('Y');
            return DataTables::of(
                DB::table('attendance_overtimes')
                    ->select(
                        'id',
                        'number',
                        'start_date',
                        'start_time',
                        'end_time',
                        'duration',
                        'filename',
                        'approved_status')
                    ->whereYear('start_date', $filterYear)
            )
                ->filter(function ($query) use ($filter) {
                    $query->where(function ($query) use ($filter) {
                        $query->where('number', 'like', "%$filter%");
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
                ->editColumn('start_time', function ($model) {
                    return substr($model->start_time, 0, 5);
                })
                ->editColumn('end_time', function ($model) {
                    return substr($model->end_time, 0, 5);
                })
                ->editColumn('duration', function ($model) {
                    return substr($model->duration, 0, 5);
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
        $getLastNumber = AttendanceOvertime::whereYear('date', date('Y'))->whereMonth('date', date('m'))
            ->orderBy('number', 'desc')
            ->pluck('number')
            ->first() ?? 0;
        //SET FORMAT FOR NUMBER LEAVE
        $lastNumber = Str::padLeft(intval(Str::substr($getLastNumber,0,4)) + 1, '4', '0').'/'.numToRoman(date('m')).'/SPL/'.date('Y');

        return view('ess.overtime.form', compact(['employee', 'lastNumber']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param OvertimeRequest $request
     * @return RedirectResponse
     */
    public function store(OvertimeRequest $request)
    {
        $file = uploadFile(
            $request->file('filename'),
            'overtime_'.Str::slug($request->input('number')).'_'.time(),
            $this->filePath);

        //CONDITION IF END TIME MORE THAN START TIME, THEN ADD 1 DAYS TO END DATE
        $startDate = Carbon::parse(resetDate($request->input('start_date')));
        $endDate = resetDate($request->input('start_date'));
        if($request->input('start_time') > $request->input('end_time'))
            $endDate = $startDate->addDays(1)->toDateString();

        //SET DATE AND TIME TO GET DIFF TIME
        $startDateTime = Carbon::parse(resetDate($request->input('start_date')). " ".$request->input('start_time'));
        $endDateTime = Carbon::parse($endDate. " ".$request->input('end_time'));
        $duration = $endDateTime->diff($startDateTime)->format('%H:%I');

        AttendanceOvertime::create([
            'number' => $request->input('number'),
            'employee_id' => $request->input('employee_id'),
            'date' => resetDate($request->input('date')),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'duration' => $duration,
            'filename' => $file,
            'description' => $request->input('description'),
            'approved_status' => 'p'
        ]);

        Alert::success('Success', 'Data Lembur berhasil disimpan');

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
        $overtime = AttendanceOvertime::with( 'employee.position')->find($id);
        $employee = Employee::find(Auth::user()->employee_id);
        $employee->position->unit_id = AppMasterData::find($employee->position->unit_id)->name ?? '';
        $employee->position->rank_id = AppMasterData::find($employee->position->rank_id)->name ?? '';
        $isCanSave = !($overtime->approved_status == 't');

        return view('ess.overtime.form', compact(['overtime', 'employee', 'isCanSave']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param OvertimeRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(OvertimeRequest $request, int $id)
    {
        $overtime = AttendanceOvertime::findOrFail($id);
        if($request->file('filename')){
            $file = uploadFile(
                $request->file('filename'),
                'overtime_'.Str::slug($request->input('number')).'_'.time(),
                $this->filePath);
            $overtime->filename = $file;
            $overtime->save();
        }

        //CONDITION IF END TIME MORE THAN START TIME, THEN ADD 1 DAYS TO END DATE
        $startDate = Carbon::parse(resetDate($request->input('start_date')));
        $endDate = resetDate($request->input('start_date'));
        if($request->input('start_time') > $request->input('end_time'))
            $endDate = $startDate->addDays(1)->toDateString();

        //SET DATE AND TIME TO GET DIFF TIME
        $startDateTime = Carbon::parse(resetDate($request->input('start_date')). " ".$request->input('start_time'));
        $endDateTime = Carbon::parse($endDate. " ".$request->input('end_time'));
        $duration = $endDateTime->diff($startDateTime)->format('%H:%I');

        $overtime->update([
            'number' => $request->input('number'),
            'employee_id' => $request->input('employee_id'),
            'date' => resetDate($request->input('date')),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'duration' => $duration,
            'description' => $request->input('description'),
            'approved_status' => 'p'
        ]);

        Alert::success('Success', 'Data Lembur berhasil disimpan');

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
        $overtime = AttendanceOvertime::findOrFail($id);
        $overtime->delete();

        Alert::success('Success', 'Data Lembur berhasil dihapus!');

        return redirect()->back();

    }
}
