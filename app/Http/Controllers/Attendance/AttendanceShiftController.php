<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\ShiftRequest;
use App\Models\Attendance\AttendanceShift;
use App\Models\Setting\AppMasterData;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;

class AttendanceShiftController extends Controller
{
    public array $statusOption;

    public function __construct()
    {
        $this->middleware('auth');
        $this->statusOption = defaultStatus();

        \View::share('statusOption', $this->statusOption);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Application|Factory|View|JsonResponse
     * @throws Exception
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            return DataTables::of(AttendanceShift::select(['id', 'code', 'name', 'start', 'end', 'description', 'location_id', 'status']))
                ->filter(function ($query) use ($filter) {
                    if (isset($filter)) $query->where('name', 'like', "%$filter%")->orWhere('code', 'like', "%$filter%");
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route(str_replace('/', '.', $this->menu_path()).'.edit', $model->id),
                        'url_destroy' => route(str_replace('/', '.', $this->menu_path()).'.destroy', $model->id),
                    ]);
                })
                ->editColumn('location_id', function ($model) {
                    return $model->location_id ? $model->location->name : 'Semua Lokasi';
                })
                ->editColumn('start', function ($model) {
                    return $model->start ? date('H:i', strtotime($model->start)) : '';
                })
                ->editColumn('end', function ($model) {
                    return $model->end ? date('H:i', strtotime($model->end)) : '';
                })
                ->addColumn('status', function ($model) {
                    return view('components.views.status', [
                        'status' => $model->status,
                    ]);
                })
                ->addIndexColumn()
                ->make();
        }

        return view('attendances.shift.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $locations = AppMasterData::whereAppMasterCategoryCode('ELK')->pluck('name', 'id')->toArray();

        return view('attendances.shift.form', compact('locations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ShiftRequest $request
     * @return JsonResponse
     */
    public function store(ShiftRequest $request)
    {
        AttendanceShift::create([
            'name' => $request->input('name'),
            'location_id' => $request->input('location_id'),
            'code' => $request->input('code'),
            'start' => $request->input('start'),
            'end' => $request->input('end'),
            'description' => $request->input('description'),
            'night_shift' => $request->input('night_shift') ? 't' : 'f',
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'success'=>'Data Shift berhasil disimpan',
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
        $data['locations'] = AppMasterData::whereAppMasterCategoryCode('ELK')->pluck('name', 'id')->toArray();
        $data['shift'] = AttendanceShift::findOrFail($id);

        return view('attendances.shift.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ShiftRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(ShiftRequest $request, int $id)
    {
        $shift = AttendanceShift::findOrFail($id);
        $shift->update([
            'name' => $request->input('name'),
            'location_id' => $request->input('location_id'),
            'code' => $request->input('code'),
            'start' => $request->input('start'),
            'end' => $request->input('end'),
            'description' => $request->input('description'),
            'night_shift' => $request->input('night_shift') ? 't' : 'f',
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'success'=>'Data Shift berhasil disimpan',
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
        $shift = AttendanceShift::findOrFail($id);
        $shift->delete();

        Alert::success('Success', 'Data shift berhasil dihapus!');

        return redirect()->back();

    }
}
