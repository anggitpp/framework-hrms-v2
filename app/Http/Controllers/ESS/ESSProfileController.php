<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\ESS\ProfileRequest;
use App\Models\Employee\Employee;
use App\Models\Setting\AppMasterData;
use App\Services\Employee\EmployeeService;
use App\Services\Setting\AppMasterDataService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class ESSProfileController extends Controller
{
    public string $photoPath;
    public string $identityPath;
    public array $genderOption;
    private EmployeeService $employeeService;
    private AppMasterDataService $appMasterDataService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->photoPath = '/uploads/employee/photo/';
        $this->identityPath = '/uploads/employee/identity/';
        $this->genderOption = ['m' => "Laki-Laki", "f" => "Perempuan"];
        $this->employeeService = new EmployeeService();
        $this->appMasterDataService = new AppMasterDataService();

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
     * Show the form for editing the specified resource.
     *
     * @return Application|Factory|View
     */
    public function edit()
    {
        $employee = $this->employeeService->getEmployeeById(Auth::user()->employee_id);
        $maritals = $this->appMasterDataService->getMasterForArray('ESPK');
        $statuses = $this->appMasterDataService->getMasterForArray('ESP');
        $religions = $this->appMasterDataService->getMasterForArray('EAG');

        return view(Str::replace('/', '.', $this->menu_path()).'.form', [
            'employee' => $employee,
            'maritals' => $maritals,
            'statuses' => $statuses,
            'religions' => $religions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProfileRequest $request
     * @return RedirectResponse
     */
    public function update(ProfileRequest $request)
    {
        $id = Auth::user()->employee_id;
        return submitDataHelper(function () use ($request, $id) {
            $this->employeeService->saveEmployee($request, $id);
        });
    }
}
