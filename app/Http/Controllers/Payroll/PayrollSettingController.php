<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\HolidayRequest;
use App\Http\Requests\Payroll\MasterRequest;
use App\Models\Attendance\AttendanceHoliday;
use App\Models\Payroll\PayrollMaster;
use App\Models\Payroll\PayrollSetting;
use App\Models\Setting\AppMasterData;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Str;
use Yajra\DataTables\DataTables;

class PayrollSettingController extends Controller
{
    public array $statusOption;

    public function __construct()
    {
        $this->middleware('auth');
        $this->statusOption = defaultStatus();

        \View::share('statusOption', $this->statusOption);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Application|Factory|View|JsonResponse
     */
    public function index(Request $request)
    {
        list($code, $codeMaster, $field) = explode(' - ', getParameterMenu($this->menu_path()));

        if($request->ajax()){
            $filter = $request->get('search')['value'];
            return DataTables::of(DB::table('app_master_data as t1')
                ->select('t1.id', 't1.name', 't2.amount')
                ->leftJoin('payroll_settings as t2', function ($join) use ($field, $code){
                    $join->on('t1.id', 't2.reference_id')
                        ->where('t2.reference_field', $field)
                        ->where('t2.code', $code);
                })
                ->where('t1.app_master_category_code', $codeMaster)
                ->orderBy('t1.order'))
                ->filter(function ($query) use ($filter) {
                    if (isset($filter)) $query->where('t1.name', 'like', "%$filter%");
                })
                ->editColumn('amount', function ($model) {
                    return setCurrency($model->amount);
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route(Str::replace('/', '.', $this->menu_path()).'.edit', $model->id),
                    ]);
                })
                ->addIndexColumn()
                ->make();
        }

        return view('payrolls.setting.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        list($code, $codeMaster, $field) = explode(' - ', getParameterMenu($this->menu_path()));

        $data['setting'] = AppMasterData::leftJoin('payroll_settings as t2', function ($join) use ($code) {
            $join->on('app_master_data.id', 't2.reference_id')
                ->where('t2.code', $code);
        })
            ->select(['app_master_data.id', 'app_master_data.name', 't2.amount'])
            ->where('app_master_data.id', $id)
            ->first();

        return view('payrolls.setting.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id)
    {
        list($code, $codeMaster, $field) = explode(' - ', getParameterMenu($this->menu_path()));

        PayrollSetting::updateOrCreate([
            'code' => $code,
            'reference_field' => $field,
            'reference_id' => $id,
        ], [
            'amount' => resetCurrency($request->get('amount')),
        ]);

        return response()->json([
            'success'=>'Data Setting berhasil disimpan',
            'url'=> route(Str::replace('/', '.', $this->menu_path()).'.index')
        ]);
    }
}
