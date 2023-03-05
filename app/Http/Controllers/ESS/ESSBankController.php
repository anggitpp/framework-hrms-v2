<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeBankRequest;
use App\Services\Employee\EmployeeBankService;
use App\Services\Employee\EmployeeService;
use App\Services\Setting\AppMasterDataService;
use Auth;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Str;

class ESSBankController extends Controller
{
    private EmployeeBankService $employeeBankService;
    private AppMasterDataService $appMasterDataService;
    private EmployeeService $employeeService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->employeeBankService = new EmployeeBankService;
        $this->appMasterDataService = new AppMasterDataService();
        $this->employeeService = new EmployeeService();

        \View::share('statusOption', defaultStatus());
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $employee = $this->employeeService->getEmployeeById(Auth::user()->employee_id);
        return view(Str::replace('/', '.', $this->menu_path()) . '.index', compact('employee'));
    }

    /**
     * @throws Exception
     */
    public function data(Request $request)
    {
        return $this->employeeBankService->data($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'banks' => $this->appMasterDataService->getMasterForArray('SMB'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeeBankRequest $request
     * @return RedirectResponse
     */
    public function store(EmployeeBankRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->employeeBankService->saveBank($request);
        });
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'bank' => $this->employeeBankService->getBankById($id),
            'banks' => $this->appMasterDataService->getMasterForArray('SMB'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeeBankRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(EmployeeBankRequest $request, int $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->employeeBankService->saveBank($request, $id);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        return deleteDataHelper(function () use ($id) {
            $this->employeeBankService->deleteBank($id);
        });
    }
}
