<?php

namespace App\Http\Controllers\Employee;

use App\Exports\GlobalExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeTrainingRequest;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeTraining;
use App\Models\Setting\AppMasterData;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Storage;
use Str;
use Yajra\DataTables\DataTables;

class EmployeeTrainingController extends Controller
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
        $user = Auth::user();

        $dataMaster = AppMasterData::whereIn('app_master_category_code', ['ELK', 'EP', 'EG', 'EMP', 'EKPL', 'ETPL'])
            ->where('status', 't')
            ->orderBy('app_master_category_code')
            ->orderBy('order')
            ->get();
        $masters = [];
        foreach ($dataMaster as $key => $value){
            $masters[$value->app_master_category_code][$value->id] = $value->name;
        }

        \Session::put('user', $user);

        return view('employees.training.index', compact('masters'));
    }

    public function data(Request $request){
        $user = \Session::get('user');

        $types = AppMasterData::whereAppMasterCategoryCode('ETPL')
            ->pluck('name', 'id')
            ->toArray();

        if($request->ajax()){
            $filterPosition = $request->get('combo_1');
            $filterRank = $request->get('combo_2');
            $filterGrade = $request->get('combo_3');
            $filterLocation = $request->get('combo_4');
            $filter = $request->get('search')['value'];

            $table = DB::table('employee_trainings as t1')
                ->join('employees as t2', 't1.employee_id', 't2.id')
                ->join('employee_positions as t3', function ($join){
                    $join->on('t2.id', 't3.employee_id');
                    $join->where('t3.status', 't');
                })
                ->select([
                    't1.id',
                    't1.employee_id',
                    't1.subject',
                    't1.institution',
                    't1.certificate_number',
                    't1.type_id',
                    't1.start_date',
                    't1.end_date',
                    't1.filename',
                    't2.name as employee_name',
                    't2.employee_number',
                    't3.position_id',
                    't3.rank_id',
                    't3.grade_id',
                    't3.location_id',
                ]);

            if(!$user->hasPermissionTo('lvl3 '.$this->menu_path()))
                $table->where('t3.leader_id', $user->employee_id);

            return DataTables::of($table)
                ->filter(function ($query) use ($filter, $filterGrade, $filterPosition, $filterRank, $filterLocation) {
                    if (isset($filter)) $query->where('t2.name', 'like', "%{$filter}%")
                        ->orWhere('t2.employee_number', 'like', "%{$filter}%")
                        ->orWhere('t1.subject', 'like', "%{$filter}%");
                    if (isset($filterPosition)) $query->where('position_id', $filterPosition);
                    if (isset($filterRank)) $query->where('rank_id', $filterRank);
                    if (isset($filterGrade)) $query->where('grade_id', $filterGrade);
                    if (isset($filterLocation)) $query->where('location_id', $filterLocation);
                })
                ->editColumn('type_id', function ($model) use ($types) {
                    return $types[$model->type_id] ?? '';
                })
                ->editColumn('start_date', function ($model) {
                    return $model->start_date ? setDate($model->start_date) : '';
                })
                ->editColumn('end_date', function ($model) {
                    return $model->end_date ? setDate($model->end_date) : '';
                })
                ->editColumn('filename', function ($model) {
                    return $model->filename ? view('components.datatables.download', [
                        'url' => $model->filename
                    ]) : '';
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route(str_replace('/', '.', $this->menu_path()).'.edit', $model->id),
                        'url_destroy' => route(str_replace('/', '.', $this->menu_path()).'.destroy', $model->id),
                        'isModal' => false,
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
        $user = \Session::get('user');

        $data['employees'] = Employee::select(['name', 'id', 'employee_number', DB::raw("CONCAT(employee_number, ' - ', name) as namaPegawai")])
            ->whereHas('position', function ($query) use ($user) {
                if(!$user->hasPermissionTo('lvl3 '.$this->menu_path())) $query->where('leader_id', $user->employee_id);
            })
            ->orderBy('name')
            ->pluck("namaPegawai", 'id')
            ->toArray();
        $data['categories'] = AppMasterData::whereAppMasterCategoryCode('EKPL')
            ->pluck('name', 'id')
            ->toArray();
        $data['types'] = AppMasterData::whereAppMasterCategoryCode('ETPL')
            ->pluck('name', 'id')
            ->toArray();

        return view('employees.training.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeeTrainingRequest $request
     * @return RedirectResponse
     */
    public function store(EmployeeTrainingRequest $request)
    {
        try {
            $filename = '';
            if($request->hasFile('filename')) {
                $resize = false;
                $extension = $request->file('filename')->extension();
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') $resize = true;

                $filename = uploadFile(
                    $request->file('filename'),
                    'employee-training_' . Str::slug($request->input('subject')) . '_' . time(),
                    $this->trainingPath, $resize);
            }
            $request->merge(['start_date' => resetDate($request->input('start_date'))]);
            $request->merge(['end_date' => resetDate($request->input('end_date'))]);

            $training = EmployeeTraining::create($request->except('filename'));
            $training->filename = $filename;
            $training->save();

            Alert::success('Success', 'Data berhasil disimpan');

            return redirect()->route(Str::replace('/', '.', $this->menu_path()).'.index');
        } catch (Exception $e) {

            DB::rollBack();

            Alert::error('Error', $e->getMessage());

            return redirect()->route(Str::replace('/', '.', $this->menu_path()).'.index');
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
        $user = \Session::get('user');

        $data['training'] = EmployeeTraining::findOrFail($id);
        $data['employees'] = Employee::select(['name', 'id', 'employee_number', DB::raw("CONCAT(employee_number, ' - ', name) as namaPegawai")])
            ->whereHas('position', function ($query) use ($user) {
                if(!$user->hasPermissionTo('lvl3 '.$this->menu_path())) $query->where('leader_id', $user->employee_id);
            })
            ->orderBy('name')
            ->pluck("namaPegawai", 'id')
            ->toArray();
        $data['categories'] = AppMasterData::whereAppMasterCategoryCode('EKPL')
            ->pluck('name', 'id')
            ->toArray();
        $data['types'] = AppMasterData::whereAppMasterCategoryCode('ETPL')
            ->pluck('name', 'id')
            ->toArray();

        return view('employees.training.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeeTrainingRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(EmployeeTrainingRequest $request, int $id)
    {
        try {
            $training = EmployeeTraining::findOrFail($id);

            if($request->get('isDelete') == 't'){
                deleteFile($this->trainingPath.$training->filename);
                $training->update([
                    'filename' => null,
                ]);
            }
            if ($request->hasFile('filename')) {
                $resize = false;
                $extension = $request->file('filename')->extension();
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') $resize = true;

                $filename = uploadFile(
                    $request->file('filename'),
                    'employee-training_' . Str::slug($request->input('subject')) . '_' . time(),
                    $this->trainingPath, $resize);

                $training->update([
                    'filename' => $filename,
                ]);
            }

            $request->merge(['start_date' => resetDate($request->input('start_date'))]);
            $request->merge(['end_date' => resetDate($request->input('end_date'))]);

            $training->update($request->except('filename'));

            Alert::success('Success', 'Data berhasil disimpan');

            return redirect()->route(Str::replace('/', '.', $this->menu_path()).'.index');
        } catch (Exception $e) {

            DB::rollBack();

            Alert::error('Error', $e->getMessage());

            return redirect()->route(Str::replace('/', '.', $this->menu_path()).'.index');
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

            DB::commit();

            Alert::success('Success', 'Data berhasil dihapus');

            return redirect()->back();
        } catch (Exception $e) {

            DB::rollBack();

            Alert::error('Error', $e->getMessage());

            return redirect()->back();
        }
    }

    public function export(Request $request)
    {
        $user = \Session::get('user');

        $categories = AppMasterData::whereAppMasterCategoryCode('EKPL')
            ->pluck('name', 'id')
            ->toArray();
        $types = AppMasterData::whereAppMasterCategoryCode('ETPL')
            ->pluck('name', 'id')
            ->toArray();

        $filterPosition = $request->get('combo_1');
        $filterRank = $request->get('combo_2');
        $filterGrade = $request->get('combo_3');
        $filterLocation = $request->get('combo_4');
        $filter = $request->get('filter');

        $sql = DB::table('employee_trainings as t1')
            ->join('employees as t2', 't1.employee_id', 't2.id')
            ->join('employee_positions as t3', function ($join){
                $join->on('t2.id', 't3.employee_id');
                $join->where('t3.status', 't');
            })
            ->select([
                't1.id',
                't1.employee_id',
                't1.subject',
                't1.institution',
                't1.certificate_number',
                't1.category_id',
                't1.type_id',
                't1.start_date',
                't1.end_date',
                't2.name as employee_name',
                't2.employee_number',
            ]);
        if($filterPosition) $sql->where('t3.position_id', $filterPosition);
        if($filterRank) $sql->where('t3.rank_id', $filterRank);
        if($filterGrade) $sql->where('t3.grade_id', $filterGrade);
        if($filterLocation) $sql->where('t3.location_id', $filterLocation);
        if($filter) $sql->where(function ($query) use ($filter) {
            $query->where('t1.subject', 'like', '%'.$filter.'%')
                ->orWhere('t2.name', 'like', '%'.$filter.'%')
                ->orWhere('t2.employee_number', 'like', '%'.$filter.'%');
        });

        if(!$user->hasPermissionTo('lvl3 '.$this->menu_path()))
            $sql->where('t3.leader_id', $user->employee_id);

        $data = [];
        $trainings = $sql->get();
        foreach ($trainings as $k => $training){
            $data[] = [
                $k + 1,
                $training->employee_number." ",
                $training->employee_name,
                $training->subject,
                $training->institution,
                $training->certificate_number,
                $categories[$training->category_id] ?? '',
                $types[$training->type_id] ?? '',
                setDate($training->start_date),
                setDate($training->end_date),
            ];
        }

        $columns = ["no", "data pegawai" => ["nip", "nama"], "data pelatihan" => ["perihal", "institusi", "no. sertifikat", "kategori", "tipe", "tanggal mulai", "tanggal selesai"]];

        $widths = [10, 20, 30, 30, 30, 30];

        $aligns = ['center', 'center', 'left', 'left', 'left', 'left', 'left', 'left', 'center', 'center'];

        return Excel::download(new GlobalExport(
            [
                'columns' => $columns,
                'widths' => $widths,
                'aligns' => $aligns,
                'data' => $data,
                'title' => 'Data Pelatihan',
            ]
        ), 'Data Pelatihan.xlsx');
    }
}
