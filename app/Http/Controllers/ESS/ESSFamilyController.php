<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeFamilyRequest;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeFamily;
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
use Storage;
use Str;
use Yajra\DataTables\DataTables;

class ESSFamilyController extends Controller
{
    public string $familyPath;
    public array $genderOption;
    public function __construct()
    {
        $this->middleware('auth');
        $this->genderOption = ['m' => "Laki-Laki", "f" => "Perempuan"];
        $this->familyPath = '/uploads/employee/family/';

        \View::share('genderOption', $this->genderOption);
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

        return view('ess.family.index', compact('employee'));
    }

    public function data(Request $request)
    {
        $relationships = AppMasterData::whereAppMasterCategoryCode('EHK')->pluck('name', 'id')->toArray();
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            return DataTables::of(EmployeeFamily::select(['id', 'relationship_id', 'name', 'birth_date', 'birth_place', 'description', 'filename'])
                ->where('employee_id', Auth::user()->employee_id))
                ->filter(function ($query) use ($filter) {
                    $query->where(function ($query) use ($filter) {
                        $query->where('name', 'like', "%$filter%");
                    });
                })
                ->editColumn('relationship_id', function ($data) use ( $relationships) {
                    return $relationships[$data->relationship_id];
                })
                ->editColumn('birth_date', function ($data) {
                    return $data->birth_date != '0000-00-00' ? setDate($data->birth_date) : '';
                })
                ->editColumn('filename', function ($model) {
                    return $model->filename ? view('components.datatables.download', [
                        'url' => $model->filename
                    ]) : '';
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
        $relationships = AppMasterData::whereAppMasterCategoryCode('EHK')->pluck('name', 'id')->toArray();

        return view('ess.family.form', compact('relationships'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeeFamilyRequest $request
     * @return JsonResponse
     */
    public function store(EmployeeFamilyRequest $request)
    {
        try {

            $request->merge(['birth_date' => $request->input('birth_date') ? resetDate($request->input('birth_date')) : '']);

            $family = EmployeeFamily::create($request->except('filename'));

            defaultUploadFile($family, $request, $this->familyPath, 'employee-family_' . Str::slug($request->input('name')) . '_' . time());

            return response()->json([
                'success'=>'Data Keluarga berhasil disimpan',
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
        $family = EmployeeFamily::findOrFail($id);
        $relationships = AppMasterData::whereAppMasterCategoryCode('EHK')->pluck('name', 'id')->toArray();

        return view('ess.family.form', [
            'family' => $family,
            'relationships' => $relationships
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeeFamilyRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(EmployeeFamilyRequest $request, int $id)
    {
        try {
            $family = EmployeeFamily::findOrFail($id);

            defaultUploadFile($family, $request, $this->familyPath, 'employee-family_' . Str::slug($request->input('name')) . '_' . time());

            $request->merge(['birth_date' => $request->input('birth_date') ? resetDate($request->input('birth_date')) : '']);

            $family->update($request->except('filename'));

            return response()->json([
                'success'=>'Data Keluarga berhasil disimpan',
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
            $family = EmployeeFamily::findOrFail($id);
            if(Storage::exists($this->familyPath.$family->filename)) Storage::delete($this->familyPath.$family->filename);
            $family->delete();

            Alert::success('Success', 'Data berhasil dihapus');

            return redirect()->back();
        } catch (Exception $e) {

            Alert::error('Error', $e->getMessage());

            return redirect()->back();
        }
    }
}
