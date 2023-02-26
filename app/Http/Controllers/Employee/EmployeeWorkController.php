<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeWorkRequest;
use App\Imports\Employee\WorkImport;
use App\Services\Employee\EmployeeService;
use App\Services\Employee\EmployeeWorkService;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Str;

class EmployeeWorkController extends Controller
{
    private AppMasterDataService $appMasterDataService;
    private EmployeeService $employeeService;
    private EmployeeWorkService $employeeWorkService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->employeeWorkService = new EmployeeWorkService();
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
        return $this->employeeWorkService->data($request);
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
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeeWorkRequest $request
     * @return RedirectResponse
     */
    public function store(EmployeeWorkRequest $request)
    {
        return submitDataHelper($this->employeeWorkService->saveWork($request));
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
            'work' => $this->employeeWorkService->getWorkById($id),
            'employees' => $this->employeeService->getEmployeesForArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeeWorkRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(EmployeeWorkRequest $request, int $id)
    {
        return submitDataHelper($this->employeeWorkService->saveWork($request, $id));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        return deleteDataHelper($this->employeeWorkService->deleteWork($id));
    }

    public function export(Request $request)
    {
        return $this->employeeWorkService->exportWork($request);
    }

    public function import()
    {
        return view('components.form.import-form', [
            'menu_path' => $this->menu_path(),
            'title' => 'Data Import Kerja',
        ]);
    }

    public function processImport(Request $request)
    {
        return importHelper(new WorkImport(), $request);
    }
}
