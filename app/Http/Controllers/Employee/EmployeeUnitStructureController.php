<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeUnitStructure;
use App\Models\Setting\AppMasterData;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;

class EmployeeUnitStructureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|JsonResponse
     */
    public function index(Request $request)
    {
        $employees = Employee::pluck('name', 'id')->toArray();

        if($request->ajax()){
            $filter = $request->get('search')['value'];
            return DataTables::of(AppMasterData::query()->where('app_master_category_code', 'EMU')
                ->select('app_master_data.id', 'app_master_data.name', 'employee_unit_structures.leader_id', 'employee_unit_structures.administration_id')
                ->leftJoin('employee_unit_structures', 'app_master_data.id', 'employee_unit_structures.unit_id'))
                ->filter(function ($query) use ($filter) {
                    if (isset($filter)) $query->where('name', 'like', "%{$filter}%");
                })
                ->editColumn('leader_id', function ($model) use ($employees) {
                    return $model->leader_id ? $employees[$model->leader_id] : '';
                })
                ->editColumn('administration_id', function ($model) use ($employees) {
                    return $model->administration_id ? $employees[$model->administration_id] : '';
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route(str_replace('/', '.', $this->menu_path()).'.edit', $model->id),
                    ]);
                })
                ->addIndexColumn()
                ->make(true);
        }
        return view('employees.unit-structure.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $data['master'] = AppMasterData::find($id);
        $data['unit'] = EmployeeUnitStructure::where('unit_id', $id)->first();
        $data['employees'] = Employee::pluck('name', 'id')->toArray();

        return view('employees.unit-structure.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id)
    {
        try {
            EmployeeUnitStructure::updateOrCreate(
                [
                    'unit_id' => $id,
                ],[
                'leader_id' => $request->input('leader_id'),
                'administration_id' => $request->input('administration_id'),
            ]);

            return response()->json([
                'success'=>'Struktur Unit berhasil diupdate',
                'url'=> route('employees.setting-unit-structures.index')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success'=>'Gagal '.$e->getMessage(),
                'url'=> route('employees.setting-unit-structures.index')
            ]);
        }
    }
}
