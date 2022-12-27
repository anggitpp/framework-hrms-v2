<?php

namespace App\Http\Controllers\Employee;

use Alert;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\SignatureRequest;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeSignatureSetting;
use App\Models\Setting\AppMasterData;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Str;
use Yajra\DataTables\DataTables;

class EmployeeSignatureSettingController extends Controller
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
            return DataTables::of(EmployeeSignatureSetting::with('location')->whereHas('employee', function ($query) use ($filter) {
                if (isset($filter)) $query->where('name', 'like', "%$filter%")->orWhere('employee_number', 'like', "%$filter%");
            })->select(['id', 'location_id', 'employee_id', 'status']))
                ->filter(function ($query) use ($filter, $filterLocation) {
                    if (isset($filterLocation)) $query->where('location_id', $filterLocation);
                })
                ->editColumn('location_id', function ($model) {
                    return $model->location->name;
                })
                ->addColumn('employee_name', function ($model) {
                    return $model->employee->name;
                })
                ->addColumn('employee_number', function ($model) {
                    return $model->employee->employee_number;
                })
                ->addColumn('employee_position', function ($model) {
                    return $model->employee->position->rank_id ? AppMasterData::find($model->employee->position->position_id)->name : '';
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

        return view('employees.signature.index', [
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
        $employees = Employee::select(['id','name', 'employee_number', DB::raw("CONCAT(employee_number, ' - ', name) as empName")])
            ->orderBy('name')
            ->pluck('empName', 'id')
            ->toArray();

        return view('employees.signature.form', [
            'locations' => $locations,
            'employees' => $employees,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SignatureRequest $request
     * @return JsonResponse
     */
    public function store(SignatureRequest $request)
    {
        EmployeeSignatureSetting::create([
            'location_id' => $request->input('location_id'),
            'employee_id' => $request->input('employee_id'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'success'=>'Tanda Tangan berhasil disimpan',
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
        $signature = EmployeeSignatureSetting::find($id);
        $locations = AppMasterData::whereAppMasterCategoryCode('ELK')->pluck('name', 'id')->toArray();
        $employees = Employee::select(['id','name', 'employee_number', DB::raw("CONCAT(employee_number, ' - ', name) as empName")])
            ->orderBy('name')
            ->pluck('empName', 'id')
            ->toArray();

        return view('employees.signature.form', [
            'signature' => $signature,
            'locations' => $locations,
            'employees' => $employees,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SignatureRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(SignatureRequest $request, int $id)
    {
        $signature = EmployeeSignatureSetting::findOrFail($id);
        $signature->update([
            'location_id' => $request->input('location_id'),
            'employee_id' => $request->input('employee_id'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'success'=>'Tanda Tangan berhasil disimpan',
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
        $signature = EmployeeSignatureSetting::findOrFail($id);
        $signature->delete();

        Alert::success('Tanda Tangan berhasil dihapus');

        return redirect()->back();
    }
}
