<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeFamilyRequest;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeFamily;
use App\Models\ESS\EssTimesheet;
use App\Models\Setting\AppMasterData;
use App\Services\Employee\EmployeeFamilyService;
use App\Services\Employee\EmployeeService;
use App\Services\Setting\AppMasterDataService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Storage;
use Str;
use Yajra\DataTables\DataTables;

class ESSFamilyController extends Controller
{
    public array $genderOption;
    private EmployeeService $employeeService;
    private EmployeeFamilyService $employeeFamilyService;
    private AppMasterDataService $appMasterDataService;
    public function __construct()
    {
        $this->middleware('auth');
        $this->employeeService = new EmployeeService();
        $this->employeeFamilyService = new EmployeeFamilyService();
        $this->appMasterDataService = new AppMasterDataService();
        $this->genderOption = ['m' => "Laki-Laki", "f" => "Perempuan"];

        \View::share('genderOption', $this->genderOption);
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
        return $this->employeeFamilyService->data($request, true);
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
     * @param EmployeeFamilyRequest $request
     * @return RedirectResponse
     */
    public function store(EmployeeFamilyRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->employeeFamilyService->saveFamily($request);
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
            'family' => $this->employeeFamilyService->getFamilyById($id),
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
        return submitDataHelper(function () use ($request, $id) {
            $this->employeeFamilyService->saveFamily($request, $id);
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
            $this->employeeFamilyService->deleteFamily($id);
        });
    }
}
