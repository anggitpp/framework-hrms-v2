<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeEducationRequest;
use App\Imports\Employee\EducationImport;
use App\Services\Employee\EmployeeEducationService;
use App\Services\Employee\EmployeeService;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Str;

class EmployeeEducationController extends Controller
{
    private EmployeeEducationService $employeeEducationService;
    private AppMasterDataService $appMasterDataService;
    private EmployeeService $employeeService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->employeeEducationService = new EmployeeEducationService();
        $this->appMasterDataService = new AppMasterDataService();
        $this->employeeService = new EmployeeService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $positions = $this->appMasterDataService->getMasterForArray('EMP');
        $ranks = $this->appMasterDataService->getMasterForArray('EP');
        $grades = $this->appMasterDataService->getMasterForArray('EG');
        $locations = $this->appMasterDataService->getMasterForArray('ELK');

        return view(Str::replace('/', '.', $this->menu_path()) . '.index', [
            'positions' => $positions,
            'ranks' => $ranks,
            'grades' => $grades,
            'locations' => $locations,
        ]);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request)
    {
        return $this->employeeEducationService->data($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'employees' => $this->employeeService->getEmployeesForArray(),
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
        return submitDataHelper($this->employeeEducationService->saveEducation($request));
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
            'employees' => $this->employeeService->getEmployeesForArray(),
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
        return submitDataHelper($this->employeeEducationService->saveEducation($request, $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        return deleteDataHelper($this->employeeEducationService->deleteEducation($id));
    }

    public function export(Request $request)
    {
        return $this->employeeEducationService->exportEducation($request);
    }

    public function import()
    {
        return view('components.form.import-form', [
            'menu_path' => $this->menu_path(),
            'title' => 'Data Import Pendidikan',
        ]);
    }

    public function processImport(Request $request)
    {
        return importHelper(new EducationImport(), $request);
    }
}
