<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeTrainingRequest;
use App\Models\Employee\EmployeeTraining;
use App\Models\Setting\AppMasterData;
use App\Services\Employee\EmployeeService;
use App\Services\Employee\EmployeeTrainingService;
use App\Services\Setting\AppMasterDataService;
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

class ESSTrainingController extends Controller
{
    private EmployeeService $employeeService;
    private EmployeeTrainingService $employeeTrainingService;
    private AppMasterDataService $appMasterDataService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->employeeService = new EmployeeService();
        $this->employeeTrainingService = new EmployeeTrainingService();
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
        return $this->employeeTrainingService->data($request, true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
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
        return submitDataHelper(function () use ($request) {
            $this->employeeTrainingService->saveTraining($request);
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
            'training' => $this->employeeTrainingService->getTrainingById($id),
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
        return submitDataHelper(function () use ($request, $id) {
            $this->employeeTrainingService->saveTraining($request, $id);
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
            $this->employeeTrainingService->deleteTraining($id);
        });
    }
}
