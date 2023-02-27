<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeContactRequest;
use App\Imports\Employee\ContactImport;
use App\Models\Setting\AppMasterData;
use App\Services\Employee\EmployeeContactService;
use App\Services\Employee\EmployeeService;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Str;

class EmployeeContactController extends Controller
{
    public array $masters;
    private EmployeeService $employeeService;
    private EmployeeContactService $employeeContactService;
    private AppMasterDataService $appMasterDataService;

    public function __construct()
    {
        $this->middleware('auth');

        $this->employeeContactService = new EmployeeContactService();
        $this->employeeService = new EmployeeService();
        $this->appMasterDataService = new AppMasterDataService();

        \View::share('statusOption', defaultStatus());
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $dataMaster = AppMasterData::whereIn('app_master_category_code', ['ELK', 'EP', 'EG', 'EMP'])
            ->where('status', 't')
            ->orderBy('app_master_category_code')
            ->orderBy('order')
            ->get();
        $masters = [];
        foreach ($dataMaster as $key => $value) {
            $masters[$value->app_master_category_code][$value->id] = $value->name;
        }

        return view(Str::replace('/', '.', $this->menu_path()) . '.index', [
            'masters' => $masters,
        ]);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request)
    {
        return $this->employeeContactService->data($request);
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
     * @param EmployeeContactRequest $request
     * @return RedirectResponse
     */
    public function store(EmployeeContactRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->employeeContactService->saveContact($request);
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
            'contact' => $this->employeeContactService->getContactById($id),
            'employees' => $this->employeeService->getEmployeesForArray(),
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
            $this->employeeContactService->deleteContact($id);
        });
    }

    public function export(Request $request)
    {
        return $this->employeeContactService->exportContact($request);
    }

    public function import()
    {
        return view('components.form.import-form', [
            'menu_path' => $this->menu_path(),
            'title' => 'Data Import Kontak Darurat',
        ]);
    }

    public function processImport(Request $request)
    {
        return importHelper(new ContactImport(), $request);
    }


}
