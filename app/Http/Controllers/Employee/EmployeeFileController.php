<?php

namespace App\Http\Controllers\Employee;

use App\Exports\GlobalExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeFileRequest;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeFile;
use App\Models\Setting\AppMasterData;
use App\Services\Employee\EmployeeFileService;
use App\Services\Employee\EmployeeService;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse as JsonResponseAlias;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Storage;
use Str;
use Yajra\DataTables\DataTables;

class EmployeeFileController extends Controller
{
    private EmployeeFileService $employeeFileService;
    private AppMasterDataService $appMasterDataService;
    private EmployeeService $employeeService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->employeeFileService = new EmployeeFileService();
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
        return $this->employeeFileService->data($request);
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
     * @param EmployeeFileRequest $request
     * @return RedirectResponse
     */
    public function store(EmployeeFileRequest $request)
    {
        return submitDataHelper($this->employeeFileService->saveFile($request), true);
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
            'file' => $this->employeeFileService->getFileById($id),
            'employees' => $this->employeeService->getEmployeesForArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeeFileRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(EmployeeFileRequest $request, int $id)
    {
        return submitDataHelper($this->employeeFileService->saveFile($request, $id), true);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        return deleteDataHelper($this->employeeFileService->deleteFile($id));
    }

    public function export(Request $request)
    {
        return $this->employeeFileService->exportFile($request);
    }
}
