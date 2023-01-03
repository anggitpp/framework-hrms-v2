<?php

namespace App\Http\Controllers\Employee;

use App\Exports\GlobalExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeEducationRequest;
use App\Http\Requests\Employee\EmployeeFamilyRequest;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeEducation;
use App\Models\Employee\EmployeeFamily;
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

class EmployeeEducationController extends Controller
{
    public array $masters;
    public string $educationPath;

    public function __construct()
    {
        $this->middleware('auth');
        $this->educationPath = '/uploads/employee/education/';
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $user = Auth::user();

        $dataMaster = AppMasterData::whereIn('app_master_category_code', ['ELK', 'EP', 'EG', 'EMP', 'EHK'])
            ->where('status', 't')
            ->orderBy('app_master_category_code')
            ->orderBy('order')
            ->get();
        $masters = [];
        foreach ($dataMaster as $key => $value){
            $masters[$value->app_master_category_code][$value->id] = $value->name;
        }

        \Session::put('user', $user);

        return view('employees.education.index', compact('masters'));
    }

    public function data(Request $request){
        $user = \Session::get('user');

        $levels = AppMasterData::whereAppMasterCategoryCode('EMJP')
            ->pluck('name', 'id')
            ->toArray();

        if($request->ajax()){
            $filterPosition = $request->get('combo_1');
            $filterRank = $request->get('combo_2');
            $filterGrade = $request->get('combo_3');
            $filterLocation = $request->get('combo_4');
            $filter = $request->get('search')['value'];

            $table = DB::table('employee_education as t1')
                ->join('employees as t2', 't1.employee_id', 't2.id')
                ->join('employee_positions as t3', function ($join){
                    $join->on('t2.id', 't3.employee_id');
                    $join->where('t3.status', 't');
                })
                ->select([
                    't1.id',
                    't1.employee_id',
                    't1.name',
                    't1.level_id',
                    't1.major',
                    't1.start_year',
                    't1.end_year',
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
                        ->orWhere('t1.name', 'like', "%{$filter}%");
                    if (isset($filterPosition)) $query->where('position_id', $filterPosition);
                    if (isset($filterRank)) $query->where('rank_id', $filterRank);
                    if (isset($filterGrade)) $query->where('grade_id', $filterGrade);
                    if (isset($filterLocation)) $query->where('location_id', $filterLocation);
                })
                ->editColumn('level_id', function ($model) use ($levels) {
                    return $levels[$model->level_id] ?? '';
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
        $data['levels'] = AppMasterData::whereAppMasterCategoryCode('EMJP')
            ->pluck('name', 'id')
            ->toArray();

        return view('employees.education.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeeEducationRequest $request
     * @return RedirectResponse
     */
    public function store(EmployeeEducationRequest $request)
    {
        try {
            $filename = '';
            if($request->hasFile('filename')) {
                $resize = false;
                $extension = $request->file('filename')->extension();
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') $resize = true;

                $filename = uploadFile(
                    $request->file('filename'),
                    'employee-education_' . Str::slug($request->input('name')) . '_' . time(),
                    $this->educationPath, $resize);
            }
            $education = EmployeeEducation::create($request->except('filename'));
            $education->filename = $filename;
            $education->save();

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

        $data['education'] = EmployeeEducation::findOrFail($id);
        $data['employees'] = Employee::select(['name', 'id', 'employee_number', DB::raw("CONCAT(employee_number, ' - ', name) as namaPegawai")])
            ->whereHas('position', function ($query) use ($user) {
                if(!$user->hasPermissionTo('lvl3 '.$this->menu_path())) $query->where('leader_id', $user->employee_id);
            })
            ->orderBy('name')
            ->pluck("namaPegawai", 'id')
            ->toArray();
        $data['levels'] = AppMasterData::whereAppMasterCategoryCode('EMJP')
            ->pluck('name', 'id')
            ->toArray();

        return view('employees.education.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeeFamilyRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(EmployeeEducationRequest $request, int $id)
    {
        try {
            $education = EmployeeEducation::findOrFail($id);

            if($request->get('isDelete') == 't'){
                deleteFile($this->educationPath.$education->filename);
                $education->update([
                    'filename' => null,
                ]);
            }
            if ($request->hasFile('filename')) {
                $resize = false;
                $extension = $request->file('filename')->extension();
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') $resize = true;

                $filename = uploadFile(
                    $request->file('filename'),
                    'employee-education_' . Str::slug($request->input('name')) . '_' . time(),
                    $this->educationPath, $resize);

                $education->update([
                    'filename' => $filename,
                ]);
            }

            $education->update($request->except('filename'));

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
            $education = EmployeeEducation::findOrFail($id);
            if(Storage::exists($this->educationPath.$education->filename)) Storage::delete($this->educationPath.$education->filename);
            $education->delete();

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

        $levels = AppMasterData::whereAppMasterCategoryCode('EMJP')
            ->pluck('name', 'id')
            ->toArray();

        $filterPosition = $request->get('combo_1');
        $filterRank = $request->get('combo_2');
        $filterGrade = $request->get('combo_3');
        $filterLocation = $request->get('combo_4');
        $filter = $request->get('filter');

        $sql = DB::table('employee_education as t1')
            ->join('employees as t2', 't1.employee_id', 't2.id')
            ->join('employee_positions as t3', function ($join){
                $join->on('t2.id', 't3.employee_id');
                $join->where('t3.status', 't');
            })
            ->select([
                't1.id',
                't1.employee_id',
                't1.name',
                't1.major',
                't1.level_id',
                't1.start_year',
                't1.end_year',
                't2.name as employee_name',
                't2.employee_number',
            ]);
        if($filterPosition) $sql->where('t3.position_id', $filterPosition);
        if($filterRank) $sql->where('t3.rank_id', $filterRank);
        if($filterGrade) $sql->where('t3.grade_id', $filterGrade);
        if($filterLocation) $sql->where('t3.location_id', $filterLocation);
        if($filter) $sql->where(function ($query) use ($filter) {
            $query->where('t1.name', 'like', '%'.$filter.'%')
                ->orWhere('t2.name', 'like', '%'.$filter.'%')
                ->orWhere('t2.employee_number', 'like', '%'.$filter.'%');
        });

        if(!$user->hasPermissionTo('lvl3 '.$this->menu_path()))
            $sql->where('t3.leader_id', $user->employee_id);

        $data = [];
        $educations = $sql->get();
        foreach ($educations as $k => $education){
            $data[] = [
                $k + 1,
                $education->employee_number." ",
                $education->employee_name,
                $education->name,
                $levels[$education->level_id] ?? '',
                $education->major,
                $education->start_year,
                $education->end_year,
            ];
        }

        $columns = ["no", "data pegawai" => ["nip", "nama"], "data pendidikan" => ["tingkatan", "nama institusi", "jurusan", "mulai", "selesai"]];

        $widths = [10, 20, 30, 20, 30];

        $aligns = ['center', 'center', 'left', 'left', 'left', 'left', 'center', 'center'];

        return Excel::download(new GlobalExport(
            [
                'columns' => $columns,
                'widths' => $widths,
                'aligns' => $aligns,
                'data' => $data,
                'title' => 'Data Pendidikan',
            ]
        ), 'Data Pendidikan.xlsx');
    }
}
