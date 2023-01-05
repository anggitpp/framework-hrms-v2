<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeTrainingRequest;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeTraining;
use App\Models\Setting\AppMasterData;
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

class ESSTrainingController extends Controller
{
    public string $trainingPath;
    public function __construct()
    {
        $this->middleware('auth');
        $this->trainingPath = '/uploads/employee/training/';
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

        return view('ess.training.index', compact('employee'));
    }

    public function data(Request $request)
    {
        $types = AppMasterData::whereAppMasterCategoryCode('ETPL')
            ->pluck('name', 'id')
            ->toArray();

        if($request->ajax()){
            $filter = $request->get('search')['value'];
            return DataTables::of(EmployeeTraining::select(['id', 'subject', 'institution', 'certificate_number', 'type_id', 'start_date', 'end_date', 'filename'])
                ->where('employee_id', Auth::user()->employee_id))
                ->filter(function ($query) use ($filter) {
                    $query->where(function ($query) use ($filter) {
                        $query->where('subject', 'like', "%$filter%")
                            ->orWhere('institution', 'like', "%$filter%")
                            ->orWhere('certificate_number', 'like', "%$filter%");
                    });
                })
                ->editColumn('type_id', function ($model) use ($types) {
                    return $types[$model->type_id] ?? '';
                })
                ->editColumn('start_date', function ($model) {
                    return $model->start_date != '0000-00-00' ? setDate($model->start_date) : '';
                })
                ->editColumn('end_date', function ($model) {
                    return $model->end_date != '0000-00-00' ? setDate($model->end_date) : '';
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
        $data['categories'] = AppMasterData::whereAppMasterCategoryCode('EKPL')
            ->pluck('name', 'id')
            ->toArray();
        $data['types'] = AppMasterData::whereAppMasterCategoryCode('ETPL')
            ->pluck('name', 'id')
            ->toArray();

        return view('ess.training.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeeTrainingRequest $request
     * @return JsonResponse
     */
    public function store(EmployeeTrainingRequest $request)
    {
        try {

            $training = EmployeeTraining::create($request->except('filename'));

            defaultUploadFile($training, $request, $this->trainingPath, 'employee-training_' . Str::slug($request->input('subject')) . '_' . time());

            $request->merge(['start_date' => resetDate($request->input('start_date'))]);
            $request->merge(['end_date' => resetDate($request->input('end_date'))]);

            return response()->json([
                'success'=>'Data Pelatihan berhasil disimpan',
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
        $training = EmployeeTraining::findOrFail($id);
        $categories = AppMasterData::whereAppMasterCategoryCode('EKPL')
            ->pluck('name', 'id')
            ->toArray();
        $types = AppMasterData::whereAppMasterCategoryCode('ETPL')
            ->pluck('name', 'id')
            ->toArray();

        return view('ess.training.form', [
            'training' => $training,
            'categories' => $categories,
            'types' => $types,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeeTrainingRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(EmployeeTrainingRequest $request, int $id)
    {
        try {
            $training = EmployeeTraining::findOrFail($id);

            defaultUploadFile($training, $request, $this->trainingPath, 'employee-training_' . Str::slug($request->input('subject')) . '_' . time());

            $request->merge(['start_date' => resetDate($request->input('start_date'))]);
            $request->merge(['end_date' => resetDate($request->input('end_date'))]);

            $training->update($request->except('filename'));

            return response()->json([
                'success'=>'Data Pelatihan berhasil disimpan',
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
            $training = EmployeeTraining::findOrFail($id);
            if(Storage::exists($this->trainingPath.$training->filename)) Storage::delete($this->trainingPath.$training->filename);
            $training->delete();

            Alert::success('Success', 'Data berhasil dihapus');

            return redirect()->back();
        } catch (Exception $e) {

            Alert::error('Error', $e->getMessage());

            return redirect()->back();
        }
    }
}
