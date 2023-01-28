<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\PayrollFixedRequest;
use App\Services\Payroll\PayrollFixedService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Str;

class PayrollFixedController extends Controller
{
    private array $statusOption;
    private PayrollFixedService $payrollFixedService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->statusOption = ['1' => 'Aktif', '0' => 'Tidak Aktif'];
        $this->payrollFixedService = new PayrollFixedService();

        \View::share('statusOption', $this->statusOption);
    }
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view(Str::replace('/', '.', $this->menu_path()).'.index');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function data(Request $request){
        return $this->payrollFixedService->dataPayrollFixeds($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view(str_replace('/', '.', $this->menu_path()).'.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PayrollFixedRequest $request
     * @return JsonResponse
     */
    public function store(PayrollFixedRequest $request)
    {
        $response = $this->payrollFixedService->savePayrollFixed($request);

        return response()->json([
            'success'=> $response['message'],
            'url'=> route(str_replace('/', '.', $this->menu_path()).'.index')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $fixed = $this->payrollFixedService->getPayrollFixedById($id);

        return view(str_replace('/', '.', $this->menu_path()).'.form', compact('fixed'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PayrollFixedRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(PayrollFixedRequest $request, int $id)
    {
        $response = $this->payrollFixedService->savePayrollFixed($request, $id);

        return response()->json([
            'success'=> $response['message'],
            'url'=> route(str_replace('/', '.', $this->menu_path()).'.index', ['combo_1' => $request->get('area_id')])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        $this->payrollFixedService->deletePayrollFixed($id);

        return redirect()->back();
    }
}
