<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\PermissionRequest;
use App\Http\Requests\Payroll\ComponentRequest;
use App\Models\Attendance\AttendancePermission;
use App\Models\Employee\Employee;
use App\Models\Payroll\PayrollComponent;
use App\Models\Payroll\PayrollMaster;
use App\Models\Setting\AppMasterData;
use App\Models\Setting\AppMenu;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;

class PayrollComponentController extends Controller
{
    public array $statusOption;
    public array $calculationTypes;
    public array $cutOffTypes;

    public function __construct()
    {
        $this->middleware('auth');
        $this->statusOption = defaultStatus();
        $this->calculationTypes = array("1" => "Table", "2" => "Proses", "3" => "Fixed", "4" => "Upload");
        $this->cutOffTypes = array("1" => "Awal - Akhir", "2" => "Custom");

        \View::share('statusOption', $this->statusOption);
        \View::share('calculationTypes', $this->calculationTypes);
        \View::share('cutOffTypes', $this->cutOffTypes);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $masters = PayrollMaster::orderBy('name')->pluck('name', 'id')->toArray();

        return view('payrolls.component.index', ['masters' => $masters]);
    }

    public function data(Request $request)
    {
        list($modul, $menu) = explode('/', $this->menu_path());
        $parameterMenu = DB::table('app_menus as t1')->join('app_moduls as t2', 't1.app_modul_id', '=', 't2.id')
            ->where('t2.target', $modul)
            ->where('t1.target', $menu)
            ->first()->parameter;

        if($request->ajax()){
            $filterMaster = $request->get('combo_1') ?? PayrollMaster::orderBy('name')->first()->id;
            $filter = $request->get('search')['value'];
            return DataTables::of(
                PayrollComponent::whereMasterId($filterMaster)->whereType($parameterMenu))
                ->filter(function ($query) use ($filter) {
                    $query->where(function ($query) use ($filter) {
                        $query->where('code', 'like', "%$filter%")->orWhere('name', 'like', "%$filter%");
                    });
                })
                ->editColumn('calculation_type', function ($data) {
                    return $this->calculationTypes[$data->calculation_type];
                })
                ->addColumn('status', function ($model) {
                    return view('components.views.status', [
                        'status' => $model->status,
                    ]);
                })
                ->addColumn('action', function ($model) use ($filterMaster) {
                    $arr = [
                        'menu_path' => $this->menu_path(),
                        'isModal' => false,
                        'url_edit' => route(Str::replace('/', '.', $this->menu_path()).'.edit', [$model->id, 'master_id' => $filterMaster]),
                        'url_destroy' => route(Str::replace('/', '.', $this->menu_path()).'.destroy', $model->id),
                    ];
                    return view('components.views.action', $arr);
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
        return view('payrolls.component.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PermissionRequest $request
     * @return RedirectResponse
     */
    public function store(ComponentRequest $request)
    {
        PayrollComponent::create([
            'master_id' => $request->get('master_id'),
            'type' => $request->get('type'),
            'code' => $request->get('code'),
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'status' => $request->get('status'),
            'calculation_type' => $request->get('calculation_type'),
            'calculation_cut_off' => $request->get('calculation_cut_off'),
            'calculation_cut_off_date_start' => $request->get('calculation_cut_off_date_start'),
            'calculation_cut_off_date_end' => $request->get('calculation_cut_off_date_end'),
            'calculation_description' => $request->get('calculation_description'),
            'calculation_amount' => $request->get('calculation_amount'),
            'calculation_amount_min' => $request->get('calculation_amount_min'),
            'calculation_amount_max' => $request->get('calculation_amount_max'),
        ]);

        Alert::success('Success', 'Data Komponen berhasil disimpan');

        return redirect()->route(Str::replace('/', '.', $this->menu_path()).'.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $component = PayrollComponent::findOrFail($id);

        return view('payrolls.component.form', ['component' => $component]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PermissionRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(ComponentRequest $request, int $id)
    {
        $component = PayrollComponent::findOrFail($id);

        $component->update([
            'code' => $request->get('code'),
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'status' => $request->get('status'),
            'calculation_type' => $request->get('calculation_type'),
            'calculation_cut_off' => $request->get('calculation_cut_off'),
            'calculation_cut_off_date_start' => $request->get('calculation_cut_off_date_start'),
            'calculation_cut_off_date_end' => $request->get('calculation_cut_off_date_end'),
            'calculation_description' => $request->get('calculation_description'),
            'calculation_amount' => $request->get('calculation_amount'),
            'calculation_amount_min' => $request->get('calculation_amount_min'),
            'calculation_amount_max' => $request->get('calculation_amount_max'),
        ]);

        Alert::success('Success', 'Data Komponen berhasil disimpan');

        return redirect()->route(Str::replace('/', '.', $this->menu_path()).'.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        $component = PayrollComponent::findOrFail($id);
        $component->delete();

        Alert::success('Success', 'Data Komponen berhasil dihapus!');

        return redirect()->back();

    }
}
