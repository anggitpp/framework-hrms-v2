<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeTrainingRequest;
use App\Imports\Employee\TrainingImport;
use App\Services\Employee\EmployeeService;
use App\Services\Employee\EmployeeTrainingService;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Str;

class EmployeeTrainingController extends Controller
{
    private EmployeeTrainingService $employeeTrainingService;
    private AppMasterDataService $appMasterDataService;
    private EmployeeService $employeeService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->employeeTrainingService = new EmployeeTrainingService();
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
        return $this->employeeTrainingService->data($request);
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
            'categories' => $this->appMasterDataService->getMasterForArray('EKPL'),
            'types' => $this->appMasterDataService->getMasterForArray('ETPL'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeeTrainingRequest $request
     * @return RedirectResponse
     */
    public function store(EmployeeTrainingRequest $request)
    {
        return submitDataHelper($this->employeeTrainingService->saveTraining($request));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'training' => $this->employeeTrainingService->getTrainingById($id),
            'employees' => $this->employeeService->getEmployeesForArray(),
            'categories' => $this->appMasterDataService->getMasterForArray('EKPL'),
            'types' => $this->appMasterDataService->getMasterForArray('ETPL'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeeTrainingRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(EmployeeTrainingRequest $request, int $id)
    {
        return submitDataHelper($this->employeeTrainingService->saveTraining($request, $id));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        return deleteDataHelper($this->employeeTrainingService->deleteTraining($id));
    }

    public function export(Request $request)
    {
        return $this->employeeTrainingService->exportTraining($request);
    }

    public function import()
    {
        return view('components.form.import-form', [
            'menu_path' => $this->menu_path(),
            'title' => 'Data Import Training',
        ]);
    }

    public function processImport(Request $request)
    {
        return importHelper(new TrainingImport(), $request);
    }
}
