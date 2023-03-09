<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Services\Employee\EmployeePositionService;
use App\Services\Employee\EmployeeService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Str;
use Exception;

class ESSPositionController extends Controller
{
    private EmployeeService $employeeService;
    private EmployeePositionService $employeePositionService;
    public function __construct()
    {
        $this->middleware('auth');
        $this->employeeService = new EmployeeService();
        $this->employeePositionService = new EmployeePositionService();
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
        return $this->employeePositionService->data($request);
    }
}
