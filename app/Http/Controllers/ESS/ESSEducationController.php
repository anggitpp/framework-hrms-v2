<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeEducationRequest;
use App\Services\Employee\EmployeeEducationService;
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

class ESSEducationController extends Controller
{
    private EmployeeService $employeeService;
    private EmployeeEducationService $employeeEducationService;
    private AppMasterDataService $appMasterDataService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->employeeService = new EmployeeService();
        $this->employeeEducationService = new EmployeeEducationService();
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
        return $this->employeeEducationService->data($request, true);
    }

    public function create()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'levels' => $this->appMasterDataService->getMasterForArray('EMJP'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeeEducationRequest $request
     * @return RedirectResponse
     */
    public function store(EmployeeEducationRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->employeeEducationService->saveEducation($request);
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
            'education' => $this->employeeEducationService->getEducationById($id),
            'levels' => $this->appMasterDataService->getMasterForArray('EMJP'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeeEducationRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(EmployeeEducationRequest $request, int $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->employeeEducationService->saveEducation($request, $id);
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
            $this->employeeEducationService->deleteEducation($id);
        });
    }
}
