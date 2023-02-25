<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeAssetRequest;
use App\Imports\Employee\AssetImport;
use App\Services\Employee\EmployeeAssetService;
use App\Services\Employee\EmployeeService;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Str;

class EmployeeAssetController extends Controller
{
    private EmployeeAssetService $employeeAssetService;
    private AppMasterDataService $appMasterDataService;
    private EmployeeService $employeeService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->employeeAssetService = new EmployeeAssetService();
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
        return $this->employeeAssetService->data($request);
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
            'categories' => $this->appMasterDataService->getMasterForArray('EKAS'),
            'types' => $this->appMasterDataService->getMasterForArray('ETAS'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeeAssetRequest $request
     * @return RedirectResponse
     */
    public function store(EmployeeAssetRequest $request)
    {
        return submitDataHelper($this->employeeAssetService->saveAsset($request));
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
            'asset' => $this->employeeAssetService->getAssetById($id),
            'employees' => $this->employeeService->getEmployeesForArray(),
            'categories' => $this->appMasterDataService->getMasterForArray('EKAS'),
            'types' => $this->appMasterDataService->getMasterForArray('ETAS'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeeAssetRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(EmployeeAssetRequest $request, int $id)
    {
        return submitDataHelper($this->employeeAssetService->saveAsset($request, $id));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        return deleteDataHelper($this->employeeAssetService->deleteAsset($id));
    }

    public function export(Request $request)
    {
        return $this->employeeAssetService->exportAsset($request);
    }

    public function import()
    {
        return view('components.form.import-form', [
            'menu_path' => $this->menu_path(),
            'title' => 'Data Import Asset',
        ]);
    }

    public function processImport(Request $request)
    {
        return importHelper(new AssetImport(), $request);
    }
}
