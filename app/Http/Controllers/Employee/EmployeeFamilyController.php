<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeFamilyRequest;
use App\Imports\Employee\FamilyImport;
use App\Services\Employee\EmployeeFamilyService;
use App\Services\Employee\EmployeeService;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Str;

class EmployeeFamilyController extends Controller
{
    private EmployeeFamilyService $employeeFamilyService;
    private AppMasterDataService $appMasterDataService;
    private EmployeeService $employeeService;
    public array $genderOption;

    public function __construct()
    {
        $this->middleware('auth');
        $this->genderOption = ['m' => "Laki-Laki", "f" => "Perempuan"];
        $this->employeeFamilyService = new EmployeeFamilyService();
        $this->appMasterDataService = new AppMasterDataService();
        $this->employeeService = new EmployeeService();

        \View::share('genderOption', $this->genderOption);
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
        return $this->employeeFamilyService->data($request);
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
            'relationships' => $this->appMasterDataService->getMasterForArray('EHK'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeeFamilyRequest $request
     * @return RedirectResponse
     */
    public function store(EmployeeFamilyRequest $request)
    {
        return submitDataHelper($this->employeeFamilyService->saveFamily($request));
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
            'family' => $this->employeeFamilyService->getFamilyById($id),
            'employees' => $this->employeeService->getEmployeesForArray(),
            'relationships' => $this->appMasterDataService->getMasterForArray('EHK'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeeFamilyRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(EmployeeFamilyRequest $request, int $id)
    {
        return submitDataHelper($this->employeeFamilyService->saveFamily($request, $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        return deleteDataHelper($this->employeeFamilyService->deleteFamily($id));
    }

    public function export(Request $request)
    {
        return $this->employeeFamilyService->exportFamily($request);
    }

    public function import()
    {
        return view('components.form.import-form', [
            'menu_path' => $this->menu_path(),
            'title' => 'Data Import Keluarga',
        ]);
    }

    public function processImport(Request $request)
    {
        return importHelper(new FamilyImport(), $request);
    }
}
