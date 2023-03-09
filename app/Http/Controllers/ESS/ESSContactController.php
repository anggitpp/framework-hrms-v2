<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeContactRequest;
use App\Services\Employee\EmployeeContactService;
use App\Services\Employee\EmployeeService;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Str;

class ESSContactController extends Controller
{
    private EmployeeService $employeeService;
    private EmployeeContactService $employeeContactService;
    private AppMasterDataService $appMasterDataService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->employeeService = new EmployeeService();
        $this->employeeContactService = new EmployeeContactService();
        $this->appMasterDataService = new AppMasterDataService();
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
        return $this->employeeContactService->data($request, true);
    }

    public function create()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'relationships' => $this->appMasterDataService->getMasterForArray('EHK'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeeContactRequest $request
     * @return RedirectResponse
     */
    public function store(EmployeeContactRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->employeeContactService->saveContact($request);
        }, true);
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
            'contact' => $this->employeeContactService->getContactById($id),
            'relationships' => $this->appMasterDataService->getMasterForArray('EHK'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeeContactRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(EmployeeContactRequest $request, int $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->employeeContactService->saveContact($request, $id);
        }, true);
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
            $this->employeeContactService->deleteContact($id);
        });
    }
}
