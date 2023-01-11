<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeAssetRequest;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeAsset;
use App\Models\Setting\AppMasterData;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Storage;
use Str;
use Yajra\DataTables\DataTables;

class ESSAssetController extends Controller
{
    public string $assetPath;
    public function __construct()
    {
        $this->middleware('auth');
        $this->assetPath = '/uploads/employee/asset/';

        \View::share('statusOption', defaultStatus());
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

        return view('ess.asset.index', compact('employee'));
    }

    public function data(Request $request)
    {
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            return DataTables::of(EmployeeAsset::select(['id', 'name', 'number', 'type_id', 'start_date', 'end_date', 'filename', 'status'])
                ->where('employee_id', Auth::user()->employee_id))
                ->filter(function ($query) use ($filter) {
                    $query->where(function ($query) use ($filter) {
                        $query->where('name', 'like', "%$filter%")
                            ->orWhere('number', 'like', "%$filter%");
                    });
                })
                ->editColumn('type_id', function ($model) {
                    return $model->type->name ?? '';
                })
                ->editColumn('start_date', function ($model) {
                    return $model->start_date != '0000-00-00' && $model->start_date ? setDate($model->start_date) : '';
                })
                ->editColumn('end_date', function ($model) {
                    return $model->end_date != '0000-00-00' && $model->end_date ? setDate($model->end_date) : '';
                })
                ->editColumn('filename', function ($model) {
                    return $model->filename ? view('components.datatables.download', [
                        'url' => $model->filename
                    ]) : '';
                })
                ->editColumn('status', function ($model) {
                    return view('components.views.status', [
                        'status' => $model->status,
                    ]);
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route(Str::replace('/', '.', $this->menu_path()).'.edit', $model->id),
                        'url_destroy' => route(Str::replace('/', '.', $this->menu_path()).'.destroy', $model->id),
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
        $data['categories'] = AppMasterData::whereAppMasterCategoryCode('EKAS')
            ->pluck('name', 'id')
            ->toArray();
        $data['types'] = AppMasterData::whereAppMasterCategoryCode('ETAS')
            ->pluck('name', 'id')
            ->toArray();

        return view('ess.asset.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeeAssetRequest $request
     * @return JsonResponse
     */
    public function store(EmployeeAssetRequest $request)
    {
        try {
            $request->merge(['date' => resetDate($request->input('date'))]);
            $request->merge(['start_date' => $request->input('start_date') ? resetDate($request->input('start_date')) : '']);
            $request->merge(['end_date' => $request->input('end_date') ? resetDate($request->input('end_date')) : '']);
            $asset = EmployeeAsset::create($request->except('filename'));

            defaultUploadFile($asset, $request, $this->assetPath, 'employee-asset_' . Str::slug($request->input('name')) . '_' . time());

            return response()->json([
                'success'=>'Data Aset berhasil disimpan',
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.index')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success'=>'Gagal, '.$e->getMessage(),
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.index')
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $asset = EmployeeAsset::findOrFail($id);
        $categories = AppMasterData::whereAppMasterCategoryCode('EKAS')
            ->pluck('name', 'id')
            ->toArray();
        $types = AppMasterData::whereAppMasterCategoryCode('ETAS')
            ->pluck('name', 'id')
            ->toArray();

        return view('ess.asset.form', [
            'asset' => $asset,
            'categories' => $categories,
            'types' => $types,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeeAssetRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(EmployeeAssetRequest $request, int $id)
    {
        try {
            $asset = EmployeeAsset::findOrFail($id);

            defaultUploadFile($asset, $request, $this->assetPath, 'employee-asset_' . Str::slug($request->input('name')) . '_' . time());

            $request->merge(['date' => resetDate($request->input('date'))]);
            $request->merge(['start_date' => $request->input('start_date') ? resetDate($request->input('start_date')) : '']);
            $request->merge(['end_date' => $request->input('end_date') ? resetDate($request->input('end_date')) : '']);

            $asset->update($request->except('filename'));

            return response()->json([
                'success'=>'Data Aset berhasil disimpan',
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.index')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success'=>'Gagal, '.$e->getMessage(),
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.index')
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        try {
            $asset = EmployeeAsset::findOrFail($id);
            if(Storage::exists($this->assetPath.$asset->filename)) Storage::delete($this->assetPath.$asset->filename);
            $asset->delete();

            Alert::success('Success', 'Data berhasil dihapus');

            return redirect()->back();
        } catch (Exception $e) {

            Alert::error('Error', $e->getMessage());

            return redirect()->back();
        }
    }
}