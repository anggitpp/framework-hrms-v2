<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\HolidayRequest;
use App\Http\Requests\Payroll\MasterRequest;
use App\Models\Attendance\AttendanceHoliday;
use App\Models\Payroll\PayrollMaster;
use App\Models\Payroll\PayrollSetting;
use App\Models\Setting\AppMasterData;
use App\Services\Payroll\PayrollSettingService;
use Exception;
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
    private PayrollSettingService $payrollSettingService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->statusOption = defaultStatus();
        $this->payrollSettingService = new PayrollSettingService();

        \View::share('statusOption', $this->statusOption);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Application|Factory|View|JsonResponse
     */
    public function index()
    {
        return view('payrolls.setting.index');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function data(Request $request){
        return $this->payrollSettingService->dataPayrollSettings($request);
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

        $data['master'] = $this->payrollSettingService->getPayrollSettingFromMasterData($code, $id);

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
        $this->payrollSettingService->savePayrollSetting($request, $id);

        return response()->json([
            'success'=>'Data Setting berhasil disimpan',
            'url'=> route(Str::replace('/', '.', $this->menu_path()).'.index')
        ]);
    }
}
