<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeWorkRequest;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeWork;
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

class ESSWorkController extends Controller
{
    public string $workPath;
    public function __construct()
    {
        $this->middleware('auth');
        $this->workPath = '/uploads/employee/work/';
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

        return view('ess.work.index', compact('employee'));
    }

    public function data(Request $request)
    {
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            return DataTables::of(EmployeeWork::select(['id', 'company', 'position', 'start_date', 'end_date', 'filename'])
                ->where('employee_id', Auth::user()->employee_id))
                ->filter(function ($query) use ($filter) {
                    $query->where(function ($query) use ($filter) {
                        $query->where('company', 'like', "%$filter%")
                            ->orWhere('position', 'like', "%$filter%");
                    });
                })
                ->editColumn('start_date', function ($model) {
                    return $model->start_date != '0000-00-00' ? setDate($model->start_date) : '';
                })
                ->editColumn('end_date', function ($model) {
                    return $model->end_date != '0000-00-00' && $model->end_date ? setDate($model->end_date) : '';
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
        return view('ess.work.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeeWorkRequest $request
     * @return JsonResponse
     */
    public function store(EmployeeWorkRequest $request)
    {
        try {
            $request->merge(['start_date' => resetDate($request->input('start_date'))]);
            $request->merge(['end_date' => $request->input('end_date') ? resetDate($request->input('end_date')) : '']);

            $work = EmployeeWork::create($request->except('filename'));

            defaultUploadFile($work, $request, $this->workPath, 'employee-work_' . Str::slug($request->input('company')) . '_' . time());

            return response()->json([
                'success'=>'Data Pekerjaan berhasil disimpan',
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
        $work = EmployeeWork::findOrFail($id);

        return view('ess.work.form', [
            'work' => $work,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeeWorkRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(EmployeeWorkRequest $request, int $id)
    {
        try {
            $work = EmployeeWork::findOrFail($id);

            defaultUploadFile($work, $request, $this->workPath, 'employee-work_' . Str::slug($request->input('company')) . '_' . time());

            $request->merge(['start_date' => resetDate($request->input('start_date'))]);
            $request->merge(['end_date' => $request->input('end_date') ? resetDate($request->input('end_date')) : '']);

            $work->update($request->except('filename'));

            return response()->json([
                'success'=>'Data Pekerjaan berhasil disimpan',
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
            $work = EmployeeWork::findOrFail($id);
            if(Storage::exists($this->workPath.$work->filename)) Storage::delete($this->workPath.$work->filename);
            $work->delete();

            Alert::success('Success', 'Data berhasil dihapus');

            return redirect()->back();
        } catch (Exception $e) {

            Alert::error('Error', $e->getMessage());

            return redirect()->back();
        }
    }
}
