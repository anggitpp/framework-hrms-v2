<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Services\Payroll\PayrollUploadService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Str;

class PayrollUploadController extends Controller
{
    private PayrollUploadService $payrollUploadService;
    public function __construct()
    {
        $this->middleware('auth');
        $this->payrollUploadService = new PayrollUploadService();
    }
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $filterYear = $request->get('filterYear') ?? date('Y');

        $datas = $this->payrollUploadService->getPayrollUploadsInMonthByYear($filterYear);

        return view('payrolls.uploads.index', [
            'filterYear' => $filterYear,
            'datas' => $datas,
        ]);
    }

    public function detail($month, $year)
    {
        return view('payrolls.uploads.detail', [
            'month' => $month,
            'year' => $year,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function dataDetail(Request $request, $month, $year)
    {
        return $this->payrollUploadService->dataDetail($request, $month, $year);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $upload = $this->payrollUploadService->getPayrollUploadById($id);
        return view('payrolls.uploads.form', [
            'id' => $id,
            'upload' => $upload,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $upload = $this->payrollUploadService->getPayrollUploadById($id);
        $arrData = [
            'code' => $upload->code,
            'month' => $upload->month,
            'year' => $upload->year,
            'component_id' => $upload->component_id,
            'employee_id' => $upload->employee_id,
            'amount' => resetCurrency($request->get('amount')),
            'description' => $request->get('description'),
        ];
        $response = $this->payrollUploadService->savePayrollUpload($arrData);

        return response()->json([
            'success'=> $response['message'],
            'url'=> route(Str::replace('/', '.', $this->menu_path()).'.detail', [$upload->month, $upload->year]),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        $this->payrollUploadService->deletePayrollUpload($id);

        return redirect()->back();
    }

    public function import()
    {
        return view('components.form.import-form', [
            'menu_path' => $this->menu_path(),
            'title' => 'Upload Lembur',
        ]);
    }

    public function processImport(Request $request)
    {
        $response = $this->payrollUploadService->processImportPayrollUpload($request);

        return response()->json([
            'success'=> $response['message'],
            'url'=> route(Str::replace('/', '.', $this->menu_path()).'.index'),
        ]);
    }
}
