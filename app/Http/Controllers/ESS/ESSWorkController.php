<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeWorkRequest;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeWork;
use App\Models\Setting\AppMasterData;
use App\Services\Employee\EmployeeService;
use App\Services\Employee\EmployeeWorkService;
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

class ESSWorkController extends Controller
{
    private EmployeeService $employeeService;
    private EmployeeWorkService $employeeWorkService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->employeeService = new EmployeeService();
        $this->employeeWorkService = new EmployeeWorkService();
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
        return $this->employeeWorkService->data($request, true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeeWorkRequest $request
     * @return RedirectResponse
     */
    public function store(EmployeeWorkRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->employeeWorkService->saveWork($request);
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
            'work' => $this->employeeWorkService->getWorkById($id),
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
        return submitDataHelper(function () use ($request, $id) {
            $this->employeeWorkService->saveWork($request, $id);
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
            $this->employeeWorkService->deleteWork($id);
        });
    }
}
