<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\ESS\TimesheetRequest;
use App\Models\Employee\Employee;
use App\Models\ESS\EssTimesheet;
use App\Models\Setting\AppMasterData;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;

class EssTimesheetController extends Controller
{
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
        $employee = Employee::find(Auth::user()->employee_id);
        $employee->position->location_id = AppMasterData::find($employee->position->location_id)->name ?? '';
        $employee->position->position_id = AppMasterData::find($employee->position->position_id)->name ?? '';
        $employee->position->grade_id = AppMasterData::find($employee->position->grade_id)->name ?? '';
        $employee->position->unit_id = AppMasterData::find($employee->position->unit_id)->name ?? '';

        return view('ess.timesheet.index', compact('employee'));
    }

    public function data(Request $request)
    {
        if($request->ajax()){
            $filterDate = $request->get('filter_1') ?? date('d/m/Y'); //GET FILTER
            $filterDate = resetDate($filterDate); //CONVERT FILTER TO DATE
            $filter = $request->get('search')['value'];
            return DataTables::of(EssTimesheet::select(['id', 'activity', 'output', 'start_time', 'end_time', 'duration', 'volume', 'type', 'description'])
                ->where('date', $filterDate)
                ->where('employee_id', Auth::user()->employee_id))
                ->filter(function ($query) use ($filter) {
                    $query->where(function ($query) use ($filter) {
                        $query->where('activity', 'like', "%$filter%")
                            ->orWhere('output', 'like', "%$filter%");
                    });
                })
                ->editColumn('start_time', function ($data) {
                    return Carbon::parse($data->start_time)->format('H:i');
                })
                ->editColumn('end_time', function ($data) {
                    return Carbon::parse($data->end_time)->format('H:i');
                })
                ->editColumn('duration', function ($data) {
                    return Carbon::parse($data->duration)->format('H:i');
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route(str_replace('/', '.', $this->menu_path()).'.edit', $model->id),
                        'url_destroy' => route(str_replace('/', '.', $this->menu_path()).'.destroy', $model->id),
                    ]);
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
        return view('ess.timesheet.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TimesheetRequest $request
     * @return JsonResponse
     */
    public function store(TimesheetRequest $request)
    {
        $duration = Carbon::parse($request->get('end_time'))->diff(Carbon::parse($request->get('start_time')))->format('%H:%I');

        EssTimesheet::create([
            'employee_id' => Auth::user()->employee_id,
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

        return response()->json([
            'success'=>'Data Kinerja berhasil disimpan',
            'url'=> route(str_replace('/', '.', $this->menu_path()).'.index')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $timesheet = EssTimesheet::findOrFail($id);
        $timesheet->start_time = Carbon::parse($timesheet->start_time)->format('H:i');
        $timesheet->end_time = Carbon::parse($timesheet->end_time)->format('H:i');

        return view('ess.timesheet.form', compact('timesheet'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TimesheetRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(TimesheetRequest $request, int $id)
    {
        $duration = Carbon::parse($request->get('end_time'))->diff(Carbon::parse($request->get('start_time')))->format('%H:%I');

        $timesheet = EssTimesheet::findOrFail($id);
        $timesheet->update([
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

        return response()->json([
            'success'=>'Data Kinerja berhasil disimpan',
            'url'=> route(str_replace('/', '.', $this->menu_path()).'.index')
        ]);
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

        Alert::success('Data Kinerja berhasil dihapus');

        return redirect()->back();
    }
}
