<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\LeaveMasterRequest;
use App\Models\Attendance\AttendanceLeaveMaster;
use App\Models\Setting\AppMasterData;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;

class AttendanceLeaveMasterController extends Controller
{
    public array $statusOption;
    public array $genderOption;

    public function __construct()
    {
        $this->middleware('auth');
        $this->statusOption = defaultStatus();
        $this->genderOption = array("a" => "Semua", "f" => "Perempuan", "m" => "Laki-laki");

        \View::share('statusOption', $this->statusOption);
        \View::share('genderOption', $this->genderOption);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('attendances.leave-master.index');
    }

    public function data(Request $request)
    {
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            return DataTables::of(AttendanceLeaveMaster::select(['id', 'name', 'balance', 'start_date', 'end_date', 'location_id', 'work_period', 'status']))
                ->filter(function ($query) use ($filter) {
                    if (isset($filter)) $query->where('name', 'like', "%$filter%");
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
                ->addColumn('period', function ($model) {
                    return setDate($model->start_date) . ' - ' . setDate($model->end_date);
                })
                ->editColumn('work_period', function ($model) {
                    return $model->work_period.' Bulan';
                })
                ->addColumn('status', function ($model) {
                    return view('components.views.status', [
                        'status' => $model->status,
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
        $locations = AppMasterData::whereAppMasterCategoryCode('ELK')->pluck('name', 'id')->toArray();

        return view('attendances.leave-master.form', compact('locations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LeaveMasterRequest $request
     * @return JsonResponse
     */
    public function store(LeaveMasterRequest $request)
    {
        AttendanceLeaveMaster::create([
            'name' => $request->input('name'),
            'balance' => $request->input('balance'),
            'start_date' => resetDate($request->input('start_date')),
            'end_date' => resetDate($request->input('end_date')),
            'location_id' => $request->input('location_id') ?? 0,
            'work_period' => $request->input('work_period'),
            'gender' => $request->input('gender'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'success'=>'Data Master Cuti berhasil disimpan',
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
        $data['master'] = AttendanceLeaveMaster::findOrFail($id);

        return view('attendances.leave-master.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LeaveMasterRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(LeaveMasterRequest $request, int $id)
    {
        $master = AttendanceLeaveMaster::findOrFail($id);
        $master->update([
            'name' => $request->input('name'),
            'balance' => $request->input('balance'),
            'start_date' => resetDate($request->input('start_date')),
            'end_date' => resetDate($request->input('end_date')),
            'location_id' => $request->input('location_id') ?? 0,
            'work_period' => $request->input('work_period'),
            'gender' => $request->input('gender'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'success'=>'Data Master Cuti berhasil disimpan',
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
        $master = AttendanceLeaveMaster::findOrFail($id);
        $master->delete();

        Alert::success('Success', 'Data Master Cuti berhasil dihapus!');

        return redirect()->back();

    }
}
