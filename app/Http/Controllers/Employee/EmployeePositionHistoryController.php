<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeePositionHistoryRequest;
use App\Models\Attendance\AttendanceShift;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeePosition;
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
use Yajra\DataTables\DataTables;

class EmployeePositionHistoryController extends Controller
{
    public array $masters;

    public function __construct()
    {
        $this->middleware('auth');
        $masters = AppMasterData::whereIn('app_master_category_code', ['ELK', 'EP', 'EG', 'ESPK', 'ESP', 'ETP', 'EMP', 'EMU'])
            ->where('status', 't')
            ->orderBy('app_master_category_code')
            ->orderBy('order')
            ->get();
        foreach ($masters as $key => $value){
            $this->masters[$value->app_master_category_code][$value->id] = $value->name;
        }

        \View::share('masters', $this->masters);
        \View::share('statusOption', defaultStatus());
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Application|Factory|View|JsonResponse
     * @throws Exception
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if($request->ajax()){
            $filterPosition = $request->get('combo_1');
            $filterRank = $request->get('combo_2');
            $filterGrade = $request->get('combo_3');
            $filterLocation = $request->get('combo_4');
            $filter = $request->get('search')['value'];

            $table = DB::table('employee_positions as t1')
                ->join('employees as t2', 't1.employee_id', 't2.id')
                ->select(['t1.id', 't1.employee_id', 't1.position_id', 't1.rank_id', 't1.grade_id', 't1.status', 't2.name', 't2.employee_number']);

            if(!$user->hasPermissionTo('lvl3 '.$this->menu_path()))
                $table->where('t1.leader_id', $user->employee_id);

            return DataTables::of($table)
                ->filter(function ($query) use ($filter, $filterGrade, $filterPosition, $filterRank, $filterLocation) {
                    if (isset($filter)) $query->where('name', 'like', "%{$filter}%")->orWhere('employee_number', 'like', "%{$filter}%");
                    if (isset($filterPosition)) $query->where('position_id', $filterPosition);
                    if (isset($filterRank)) $query->where('rank_id', $filterRank);
                    if (isset($filterGrade)) $query->where('grade_id', $filterGrade);
                    if (isset($filterLocation)) $query->where('location_id', $filterLocation);
                })
                ->editColumn('position_id', function ($model) {
                    return $this->masters['EMP'][$model->position_id] ?? '';
                })
                ->editColumn('rank_id', function ($model) {
                    return $this->masters['EP'][$model->rank_id] ?? '';
                })
                ->editColumn('grade_id', function ($model) {
                    return $this->masters['EG'][$model->grade_id] ?? '';
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route(str_replace('/', '.', $this->menu_path()).'.edit', $model->id),
                        'url_destroy' => route(str_replace('/', '.', $this->menu_path()).'.destroy', $model->id),
                        'isModal' => false,
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

        \Session::put('user', $user);

        return view('employees.position-history.index');
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
        $data['shifts'] = AttendanceShift::pluck('name', 'id')->toArray();

        return view('employees.position-history.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeePositionHistoryRequest $request
     * @return RedirectResponse
     */
    public function store(EmployeePositionHistoryRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->input('sk_date') ? $request->merge(['sk_date' => resetDate($request->input('sk_date'))]) : null;
            $request->input('start_date') ? $request->merge(['start_date' => resetDate($request->input('start_date'))]) : null;
            $request->input('end_date') ? $request->merge(['end_date' => resetDate($request->input('end_date'))]) : null;

            $position = EmployeePosition::create($request->all());

            if($request->input('status') == 't') EmployeePosition::where('employee_id', $request->input('employee_id'))->where('id', '!=', $position->id)->update(['status' => 'f']);

            DB::commit();

            Alert::success('Success', 'Data berhasil disimpan');

            return redirect()->route('employees.position-histories.index');
        } catch (Exception $e) {

            DB::rollBack();

            Alert::error('Error', $e->getMessage());

            return redirect()->route('employees.position-histories.index');
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

        $data['position'] = EmployeePosition::findOrFail($id);
        $data['employees'] = Employee::select(['name', 'id', 'employee_number', DB::raw("CONCAT(employee_number, ' - ', name) as namaPegawai")])
            ->whereHas('position', function ($query) use ($user) {
                if(!$user->hasPermissionTo('lvl3 '.$this->menu_path())) $query->where('leader_id', $user->employee_id);
            })
            ->orderBy('name')
            ->pluck("namaPegawai", 'id')
            ->toArray();
        $data['shifts'] = AttendanceShift::pluck('name', 'id')->toArray();

        return view('employees.position-history.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeePositionHistoryRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(EmployeePositionHistoryRequest $request, int $id)
    {
        DB::beginTransaction();
        try {
            $request->input('sk_date') ? $request->merge(['sk_date' => resetDate($request->input('sk_date'))]) : null;
            $request->input('start_date') ? $request->merge(['start_date' => resetDate($request->input('start_date'))]) : null;
            $request->input('end_date') ? $request->merge(['end_date' => resetDate($request->input('end_date'))]) : null;

            $position = EmployeePosition::findOrFail($id);
            $position->update($request->all());

            if($request->input('status') == 't') EmployeePosition::where('employee_id', $request->input('employee_id'))->where('id', '!=', $position->id)->update(['status' => 'f']);

            DB::commit();

            Alert::success('Success', 'Data berhasil disimpan');

            return redirect()->route('employees.position-histories.index');
        } catch (Exception $e) {

            DB::rollBack();

            Alert::error('Error', $e->getMessage());

            return redirect()->route('employees.position-histories.index');
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
    DB::beginTransaction();
        try {
            $position = EmployeePosition::findOrFail($id);
            $position->delete();

            DB::commit();

            Alert::success('Success', 'Data berhasil dihapus');

            return redirect()->route('employees.position-histories.index');
        } catch (Exception $e) {

            DB::rollBack();

            Alert::error('Error', $e->getMessage());

            return redirect()->route('employees.position-histories.index');
        }
    }

    public function subMasters(int $id)
    {
        return AppMasterData::where('app_master_category_code', 'EG')
            ->where('parent_id', $id)
            ->select(['id', 'name'])
            ->orderBy('order')
            ->get();
    }
}
