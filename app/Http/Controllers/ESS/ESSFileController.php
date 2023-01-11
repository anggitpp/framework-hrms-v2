<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeFileRequest;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeFile;
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

class ESSFileController extends Controller
{
    public string $filePath;
    public function __construct()
    {
        $this->middleware('auth');
        $this->filePath = '/uploads/employee/file/';
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

        return view('ess.file.index', compact('employee'));
    }

    public function data(Request $request)
    {
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            return DataTables::of(EmployeeFile::select(['id', 'name', 'description', 'filename'])
                ->where('employee_id', Auth::user()->employee_id))
                ->filter(function ($query) use ($filter) {
                    $query->where(function ($query) use ($filter) {
                        $query->where('name', 'like', "%$filter%");
                    });
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
        return view('ess.file.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeeFileRequest $request
     * @return JsonResponse
     */
    public function store(EmployeeFileRequest $request)
    {
        try {
            $file = EmployeeFile::create($request->except('filename'));

            defaultUploadFile($file, $request, $this->filePath, 'employee-file_' . Str::slug($request->input('name')) . '_' . time());

            return response()->json([
                'success'=>'Data File berhasil disimpan',
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
        $file = EmployeeFile::findOrFail($id);

        return view('ess.file.form', [
            'file' => $file,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeeFileRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(EmployeeFileRequest $request, int $id)
    {
        try {
            $file = EmployeeFile::findOrFail($id);

            defaultUploadFile($file, $request, $this->filePath, 'employee-file_' . Str::slug($request->input('name')) . '_' . time());

            $file->update($request->except('filename'));

            return response()->json([
                'success'=>'Data File berhasil disimpan',
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
            $file = EmployeeFile::findOrFail($id);
            if(Storage::exists($this->filePath.$file->filename)) Storage::delete($this->filePath.$file->filename);
            $file->delete();

            Alert::success('Success', 'Data berhasil dihapus');

            return redirect()->back();
        } catch (Exception $e) {

            Alert::error('Error', $e->getMessage());

            return redirect()->back();
        }
    }
}