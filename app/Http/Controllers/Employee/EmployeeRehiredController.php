<?php

namespace App\Http\Controllers\Employee;

use App\Exports\GlobalExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeweRehiredRequest;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeRehired;
use App\Models\Setting\AppMasterData;
use App\Models\Setting\AppParameter;
use Carbon\Carbon;
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

class EmployeeRehiredController extends Controller
{
    public string $rehiredPath;
    public array $arrApprove;

    public function __construct()
    {
        $this->middleware('auth');
        $this->rehiredPath = '/uploads/employee/rehired/';
        $this->arrApprove = defaultStatusApproval();

        \View::share('arrApprove', $this->arrApprove);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $user = Auth::user();

        $dataMaster = AppMasterData::whereIn('app_master_category_code', ['ELK', 'EP', 'EG', 'EMP', 'EKAS', 'ETAS'])
            ->where('status', 't')
            ->orderBy('app_master_category_code')
            ->orderBy('order')
            ->get();
        $masters = [];
        foreach ($dataMaster as $key => $value){
            $masters[$value->app_master_category_code][$value->id] = $value->name;
        }

        \Session::put('user', $user);

        return view('employees.rehired.index', [
            'masters' => $masters,
            'filterYear' => Carbon::now()->format('Y'),
        ]);
    }

    public function data(Request $request){
        $user = \Session::get('user');

        if($request->ajax()){
            $filterPosition = $request->get('combo_1');
            $filterRank = $request->get('combo_2');
            $filterGrade = $request->get('combo_3');
            $filterLocation = $request->get('combo_4');
            $filter = $request->get('search')['value'];
            $filterYear = $request->get('filter_1');
            $startDate = Carbon::create($filterYear, 1, 1)->format('Y-m-d');
            $endDate = Carbon::create($filterYear, 12, 31)->format('Y-m-d');

            $table = DB::table('employee_rehireds as t1')
                ->join('employees as t2', 't1.employee_id', 't2.id')
                ->join('employee_positions as t3', function ($join){
                    $join->on('t2.id', 't3.employee_id');
                    $join->where('t3.status', 't');
                })
                ->select([
                    't1.id',
                    't1.employee_id',
                    't1.date',
                    't1.number',
                    't1.effective_date',
                    't1.filename',
                    't1.approved_status',
                    't2.name',
                    't2.employee_number',
                    't3.position_id',
                    't3.rank_id',
                    't3.grade_id',
                    't3.location_id',
                ]);

            if(!$user->hasPermissionTo('lvl3 '.$this->menu_path()))
                $table->where('t3.leader_id', $user->employee_id);

            return DataTables::of($table)
                ->filter(function ($query) use ($filter, $filterGrade, $filterPosition, $filterRank, $filterLocation, $startDate, $endDate) {
                    if (isset($filter)) $query->where('t2.name', 'like', "%{$filter}%")
                        ->orWhere('t2.employee_number', 'like', "%{$filter}%")
                        ->orWhere('t1.number', 'like', "%{$filter}%");
                    $query->whereBetween('t1.date', [$startDate, $endDate]);
                    if (isset($filterPosition)) $query->where('position_id', $filterPosition);
                    if (isset($filterRank)) $query->where('rank_id', $filterRank);
                    if (isset($filterGrade)) $query->where('grade_id', $filterGrade);
                    if (isset($filterLocation)) $query->where('location_id', $filterLocation);
                })
                ->editColumn('date', function ($model) {
                    return $model->date != '0000-00-00' ? setDate($model->date) : '';
                })
                ->editColumn('effective_date', function ($model) {
                    return $model->effective_date != '0000-00-00' ? setDate($model->effective_date) : '';
                })
                ->editColumn('filename', function ($model) {
                    return $model->filename ? view('components.datatables.download', [
                        'url' => $model->filename
                    ]) : '';
                })
                ->editColumn('approved_status', function ($model) {
                    return view('components.views.approve-status', [
                        'status' => $model->approved_status,
                    ]);
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route(str_replace('/', '.', $this->menu_path()).'.edit', $model->id),
                        'url_destroy' => route(str_replace('/', '.', $this->menu_path()).'.destroy', $model->id),
                        'url_slot' => route(Str::replace('/', '.', $this->menu_path()).'.approve', $model->id),
                        'icon_slot' => 'fa-solid fa-check',
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
            ->whereNot('status_id', AppParameter::whereCode('SAP')->first()->value)
            ->orderBy('name')
            ->pluck("namaPegawai", 'id')
            ->toArray();
        $getLastNumber = EmployeeRehired::whereYear('date', date('Y'))->whereMonth('date', date('m'))
            ->orderBy('number', 'desc')
            ->pluck('number')
            ->first() ?? 0;
        //SET FORMAT FOR NUMBER LEAVE
        $data['lastNumber'] = Str::padLeft(intval(Str::substr($getLastNumber,0,4)) + 1, '4', '0').'/'.numToRoman(date('m')).'/EX/'.date('Y');

        return view('employees.rehired.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeweRehiredRequest $request
     * @return RedirectResponse
     */
    public function store(EmployeweRehiredRequest $request)
    {
        try {
            $filename = '';
            if($request->hasFile('filename')) {
                $resize = false;
                $extension = $request->file('filename')->extension();
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') $resize = true;

                $filename = uploadFile(
                    $request->file('filename'),
                    'employee-rehired_' . Str::slug($request->input('name')) . '_' . time(),
                    $this->rehiredPath, $resize);
            }
            $request->merge(['date' => resetDate($request->input('date'))]);
            $request->merge(['effective_date' => resetDate($request->input('effective_date'))]);
            $request->merge(['approved_status' => 'p']);

            $rehired = EmployeeRehired::create($request->except('filename'));
            $rehired->filename = $filename;
            $rehired->save();

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

        $data['rehired'] = EmployeeRehired::findOrFail($id);
        $data['rehired']->employee->position->unit_id = AppMasterData::find($data['rehired']->employee->position->unit_id)->name;
        $data['rehired']->employee->position->rank_id = AppMasterData::find($data['rehired']->employee->position->rank_id)->name;
        $data['employees'] = Employee::select(['name', 'id', 'employee_number', DB::raw("CONCAT(employee_number, ' - ', name) as namaPegawai")])
            ->whereHas('position', function ($query) use ($user) {
                if(!$user->hasPermissionTo('lvl3 '.$this->menu_path())) $query->where('leader_id', $user->employee_id);
            })
            ->whereNot('status_id', AppParameter::whereCode('SAP')->first()->value)
            ->orderBy('name')
            ->pluck("namaPegawai", 'id')
            ->toArray();

        return view('employees.rehired.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeweRehiredRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(EmployeweRehiredRequest $request, int $id)
    {
        try {
            $rehired = EmployeeRehired::findOrFail($id);

            if($request->get('isDelete') == 't'){
                deleteFile($this->rehiredPath.$rehired->filename);
                $rehired->update([
                    'filename' => null
                ]);
            }
            if ($request->hasFile('filename')) {
                $resize = false;
                $extension = $request->file('filename')->extension();
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') $resize = true;

                $filename = uploadFile(
                    $request->file('filename'),
                    'employee-rehired_' . Str::slug($request->input('name')) . '_' . time(),
                    $this->rehiredPath, $resize);

                $rehired->update([
                    'filename' => $filename,
                ]);
            }

            $request->merge(['date' => resetDate($request->input('date'))]);
            $request->merge(['effective_date' => resetDate($request->input('effective_date'))]);

            $rehired->update($request->except('filename'));

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
            $rehired = EmployeeRehired::findOrFail($id);
            if(Storage::exists($this->rehiredPath.$rehired->filename)) Storage::delete($this->rehiredPath.$rehired->filename);
            $rehired->delete();

            DB::commit();

            Alert::success('Success', 'Data berhasil dihapus');

            return redirect()->back();
        } catch (Exception $e) {

            DB::rollBack();

            Alert::error('Error', $e->getMessage());

            return redirect()->back();
        }
    }

    public function employee(Request $request)
    {
        //GET DATA EMPLOYEE
        $employee = Employee::with('position')
            ->where('id', $request->input('employee_id'))
            ->first();

        //OVERIDE RANK FROM MASTER DATA
        $employee->position->rank_id = AppMasterData::where('id', $employee->position->rank_id)->first()->name;
        //OVERIDE UNIT FROM MASTER DATA
        $employee->position->unit_id = AppMasterData::where('id', $employee->position->unit_id)->first()->name;

        return $employee;
    }

    public function approve(int $id)
    {
        $data['data'] = EmployeeRehired::findOrFail($id);
        $data['data']->approved_status = $data['data']->approved_status == 'p' ? 't' : $data['data']->approved_status;
        $data['actionRoute'] = route(str_replace('/', '.', $this->menu_path()).'.updateApprove', $id);
        $data['title'] = 'Approve Pengaktifan Pegawai';

        return view('components.form.approve-form', $data);
    }

    public function updateApprove(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            $rehired = EmployeeRehired::findOrFail($id);
            $rehired->update([
                'approved_status' => $request->input('approved_status'),
                'approved_by' => Auth::user()->id,
                'approved_date' => resetDate($request->input('approved_date')),
                'approved_note' => $request->input('approved_note')
            ]);

            if($rehired->approved_status == 't'){
                $employee = Employee::find($rehired->employee_id);
                $employee->update([
                    'status_id' => AppParameter::whereCode('SAP')->first()->value,
                    'leave_date' => '',
                    'join_date' => $rehired->effective_date,
                ]);
            }

            DB::commit();

            return response()->json([
                'success'=>'Data Pengaktifan Pegawai berhasil disetujui',
                'url'=> route(str_replace('/', '.', $this->menu_path()).'.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error'=>'Error: '. $e->getMessage(),
                'url'=> route(str_replace('/', '.', $this->menu_path()).'.index')
            ]);
        }
    }

    public function export(Request $request)
    {
        $user = \Session::get('user');

        $reasons = AppMasterData::whereAppMasterCategoryCode('EKPP')
            ->pluck('name', 'id')
            ->toArray();
        $types = AppMasterData::whereAppMasterCategoryCode('ESP')
            ->pluck('name', 'id')
            ->toArray();

        $filterPosition = $request->get('combo_1');
        $filterRank = $request->get('combo_2');
        $filterGrade = $request->get('combo_3');
        $filterLocation = $request->get('combo_4');
        $filter = $request->get('filter');
        $filterYear = $request->get('filter_1');
        $startDate = Carbon::create($filterYear, 1, 1)->format('Y-m-d');
        $endDate = Carbon::create($filterYear, 12, 31)->format('Y-m-d');

        $sql = DB::table('employee_rehireds as t1')
            ->join('employees as t2', 't1.employee_id', 't2.id')
            ->join('employee_positions as t3', function ($join){
                $join->on('t2.id', 't3.employee_id');
                $join->where('t3.status', 't');
            })
            ->select([
                't1.id',
                't1.employee_id',
                't1.date',
                't1.number',
                't1.type_id',
                't1.reason_id',
                't1.effective_date',
                't1.filename',
                't1.approved_status',
                't2.name',
                't2.employee_number',
            ]);
        $sql->whereBetween('t1.date', [$startDate, $endDate]);
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
        $rehireds = $sql->get();
        foreach ($rehireds as $k => $rehired){
            $approvedStatus = $rehired->approved_status == 'p' ? 'Pending' : ($rehired->approved_status == 't' ? 'Disetujui' : 'Ditolak');
            $data[] = [
                $k + 1,
                $rehired->number,
                $rehired->employee_number." ",
                $rehired->name,
                setDate($rehired->date),
                $reasons[$rehired->reason_id] ?? '',
                $types[$rehired->type_id] ?? '',
                setDate($rehired->effective_date),
                $approvedStatus,
            ];
        }

        $columns = ["no", "nomor", "nip", "nama", "tanggal", "tanggal efektif", "status"];

        $widths = [10, 15, 20, 30, 15, 15, 15];

        $aligns = ['center', 'center', 'left', 'left', 'center', 'center'];

        return Excel::download(new GlobalExport(
            [
                'columns' => $columns,
                'widths' => $widths,
                'aligns' => $aligns,
                'data' => $data,
                'title' => 'Data Pengaktifan Pegawai',
            ]
        ), 'Data Pengaktifan Pegawai.xlsx');
    }
}
