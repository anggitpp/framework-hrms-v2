<?php

namespace App\Http\Controllers\Attendance;

use Alert;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\MachineRequest;
use App\Models\Attendance\AttendanceMachineSetting;
use App\Models\Setting\AppMasterData;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Str;
use Yajra\DataTables\DataTables;

class AttendanceMachineController extends Controller
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
     * @return Application|Factory|View|JsonResponse
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $filterLocation = $request->get('combo_1');
            $filter = $request->get('search')['value'];
            return DataTables::of(AttendanceMachineSetting::with('location')->select(['id', 'location_id', 'serial_number', 'name', 'ip_address', 'status']))
                ->filter(function ($query) use ($filter, $filterLocation) {
                    if (isset($filter)) $query->where('name', 'like', "%$filter%");
                    if (isset($filterLocation)) $query->where('location_id', $filterLocation);
                })
                ->editColumn('location_id', function ($model) {
                    return $model->location->name;
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route(Str::replace('/', '.', $this->menu_path()).'.edit', $model->id),
                        'url_destroy' => route(Str::replace('/', '.', $this->menu_path()).'.destroy', $model->id),
                    ]);
                })
                ->addColumn('status', function ($model) {
                    return view('components.views.status', [
                        'status' => $model->status,
                    ]);
                })
                ->addIndexColumn()
                ->make(true);
        }

        $locations = AppMasterData::whereAppMasterCategoryCode('ELK')->pluck('name', 'id')->toArray();

        return view('attendances.machine.index', [
            'locations' => $locations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $locations = AppMasterData::whereAppMasterCategoryCode('ELK')->pluck('name', 'id')->toArray();

        return view('attendances.machine.form', [
            'locations' => $locations,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MachineRequest $request
     * @return JsonResponse
     */
    public function store(MachineRequest $request)
    {
        AttendanceMachineSetting::create([
            'location_id' => $request->input('location_id'),
            'name' => $request->input('name'),
            'serial_number' => $request->input('serial_number'),
            'ip_address' => $request->input('ip_address'),
            'address' => $request->input('address'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'success'=>'Mesin Absen berhasil disimpan',
            'url'=> route(Str::replace('/', '.', $this->menu_path()).'.index')
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
        $machine = AttendanceMachineSetting::find($id);
        $locations = AppMasterData::whereAppMasterCategoryCode('ELK')->pluck('name', 'id')->toArray();

        return view('attendances.machine.form', [
            'machine' => $machine,
            'locations' => $locations,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MachineRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(MachineRequest $request, int $id)
    {
        $machine = AttendanceMachineSetting::findOrFail($id);
        $machine->update([
            'location_id' => $request->input('location_id'),
            'name' => $request->input('name'),
            'serial_number' => $request->input('serial_number'),
            'ip_address' => $request->input('ip_address'),
            'address' => $request->input('address'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'success'=>'Mesin Absen berhasil disimpan',
            'url'=> route(Str::replace('/', '.', $this->menu_path()).'.index')
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
        $machine = AttendanceMachineSetting::findOrFail($id);
        $machine->delete();

        Alert::success('Mesin Absen berhasil dihapus');

        return redirect()->back();
    }
}
