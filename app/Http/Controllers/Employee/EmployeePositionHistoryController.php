<?php

namespace App\Http\Controllers\Employee;

use App\Exports\GlobalExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeePositionHistoryRequest;
use App\Imports\Employee\PositionImport;
use App\Models\Attendance\AttendanceShift;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeePosition;
use App\Models\Setting\AppMasterData;
use App\Services\Attendance\AttendanceShiftService;
use App\Services\Employee\EmployeePositionService;
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
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Str;
use Yajra\DataTables\DataTables;

class EmployeePositionHistoryController extends Controller
{
    private EmployeePositionService $employeePositionService;
    private AppMasterDataService $appMasterDataService;
    private EmployeeService $employeeService;
    private AttendanceShiftService $attendanceShiftService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->employeePositionService = new EmployeePositionService();
        $this->appMasterDataService = new AppMasterDataService();
        $this->employeeService = new EmployeeService();
        $this->attendanceShiftService = new AttendanceShiftService();

        \View::share('statusOption', defaultStatus());
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|JsonResponse
     */
    public function index(): Factory|View|JsonResponse|Application
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
        return $this->employeePositionService->data($request);
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
            'positions' => $this->appMasterDataService->getMasterForArray('EMP'),
            'ranks' => $this->appMasterDataService->getMasterForArray('EP'),
            'grades' => $this->appMasterDataService->getMasterForArray('EG'),
            'locations' => $this->appMasterDataService->getMasterForArray('ELK'),
            'units' => $this->appMasterDataService->getMasterForArray('EMU'),
            'types' => $this->appMasterDataService->getMasterForArray('ETP'),
            'shifts' => $this->attendanceShiftService->getShiftsForArray(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeePositionHistoryRequest $request
     * @return RedirectResponse
     */
    public function store(EmployeePositionHistoryRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->employeePositionService->savePosition($request);
        });
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
            'position' => $this->employeePositionService->getPositionById($id),
            'employees' => $this->employeeService->getEmployeesForArray(),
            'positions' => $this->appMasterDataService->getMasterForArray('EMP'),
            'ranks' => $this->appMasterDataService->getMasterForArray('EP'),
            'grades' => $this->appMasterDataService->getMasterForArray('EG'),
            'locations' => $this->appMasterDataService->getMasterForArray('ELK'),
            'units' => $this->appMasterDataService->getMasterForArray('EMU'),
            'types' => $this->appMasterDataService->getMasterForArray('ETP'),
            'shifts' => $this->attendanceShiftService->getShiftsForArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeePositionHistoryRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(EmployeePositionHistoryRequest $request, int $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->employeePositionService->savePosition($request, $id);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        return deleteDataHelper(function () use ($id) {
            $this->employeePositionService->deletePosition($id);
        });
    }

    public function export(Request $request)
    {
        return $this->employeePositionService->exportPosition($request);
    }

    public function import()
    {
        return view('components.form.import-form', [
            'menu_path' => $this->menu_path(),
            'title' => 'Data Import Position',
        ]);
    }

    public function processImport(Request $request)
    {
        return importHelper(new PositionImport(), $request);
    }
}
