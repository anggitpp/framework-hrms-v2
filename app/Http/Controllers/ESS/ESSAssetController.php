<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeAssetRequest;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeAsset;
use App\Models\Setting\AppMasterData;
use App\Services\Employee\EmployeeAssetService;
use App\Services\Employee\EmployeeService;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Storage;
use Str;
use Yajra\DataTables\DataTables;

class ESSAssetController extends Controller
{
    private EmployeeService $employeeService;
    private EmployeeAssetService $employeeAssetService;
    private AppMasterDataService $appMasterDataService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->employeeService = new EmployeeService();
        $this->employeeAssetService = new EmployeeAssetService();
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
        $employee = $this->employeeService->getEmployeeById(Auth::user()->employee_id);

        return view(Str::replace('/', '.', $this->menu_path()) . '.index', compact('employee'));
    }

    /**
     * @throws Exception
     */
    public function data(Request $request)
    {
        return $this->employeeAssetService->data($request, true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
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
        return submitDataHelper(function () use ($request) {
            $this->employeeAssetService->saveAsset($request);
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
            'asset' => $this->employeeAssetService->getAssetById($id),
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
        return submitDataHelper(function () use ($request, $id) {
            $this->employeeAssetService->saveAsset($request, $id);
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
            $this->employeeAssetService->deleteAsset($id);
        });
    }
}
