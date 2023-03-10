<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeFileRequest;
use App\Services\Employee\EmployeeFileService;
use App\Services\Employee\EmployeeService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Str;

class ESSFileController extends Controller
{
    private EmployeeService $employeeService;
    private EmployeeFileService $employeeFileService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->employeeService = new EmployeeService();
        $this->employeeFileService = new EmployeeFileService();
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
        return $this->employeeFileService->data($request);
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
     * @param EmployeeFileRequest $request
     * @return RedirectResponse
     */
    public function store(EmployeeFileRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->employeeFileService->saveFile($request);
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
            'file' => $this->employeeFileService->getFileById($id),
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
        return submitDataHelper(function () use ($request, $id) {
            $this->employeeFileService->saveFile($request, $id);
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
            $this->employeeFileService->deleteFile($id);
        });
    }
}
