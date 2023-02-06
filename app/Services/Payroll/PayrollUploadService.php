<?php

namespace App\Services\Payroll;

use Alert;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\PayrollFixedRequest;
use App\Imports\Payroll\UploadImport;
use App\Models\Payroll\PayrollComponent;
use App\Models\Payroll\PayrollFixed;
use App\Models\Payroll\PayrollMaster;
use App\Models\Payroll\PayrollUpload;
use App\Models\Setting\User;
use App\Repositories\Payroll\PayrollFixedRepository;
use App\Repositories\Payroll\PayrollUploadRepository;
use Excel;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PayrollUploadService extends Controller
{
    private PayrollUploadRepository $payrollUploadRepository;
    public function __construct()
    {
        $this->payrollUploadRepository = new PayrollUploadRepository();
    }

    public function getPayrollUploads() : Builder
    {
        return $this->payrollUploadRepository->getPayrollUploads();
    }

    public function getPayrollUploadById(int $id) : PayrollUpload
    {
        return $this->payrollUploadRepository->getPayrollUploadById($id);
    }

    public function getPayrollUploadsDetail(string $month, string $year): Builder
    {
        $code = getParameterMenu($this->menu_path());

        return $this->payrollUploadRepository->getPayrollUploadsDetailByMonthAndYearWithEmployeeeAndPosition($month, $year, $code);
    }

    /**
     * @throws Exception
     */
    public function dataDetail(Request $request, $month, $year): JsonResponse
    {
        if ($request->ajax()) {
            $sql = $this->getPayrollUploadsDetail($month, $year);

            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)){
                    $query->where(function ($query) use ($filter) {
                        $query->where('employees.name', 'like', "%{$filter}%")->orWhere('app_master_data.name', 'like', "%{$filter}%");
                    });
                }
            };


            return generateDatatable($sql, $queryFilter, [
                ['name' => 'amount', 'type' => 'currency'],
            ], true);
        }
    }

    public function getPayrollUploadsInMonthByYear(int $year) : Collection
    {
        $code = getParameterMenu($this->menu_path());
        $datas = [];

        $uploads = $this->payrollUploadRepository->getPayrollUploadsByYear($year, $code)->get();

        for ($i = 1; $i <= 12; $i++) {
            $datas[$i]['month'] = $i;
            $datas[$i]['time'] = $uploads->where('month', $i)->first()->updated_at ?? '';
            $datas[$i]['by'] = !empty($uploads->where('month', $i)->first()->updated_by) ? User::find($uploads->where('month', $i)->first()->updated_by)->name : '';
            $datas[$i]['totalEmployee'] = $uploads->where('month', $i)->count();
            $datas[$i]['totalAmount'] = $uploads->where('month', $i)->sum('amount');
        }

        return collect($datas);
    }

    public function savePayrollUpload(array $fields): array
    {
        $targetFields = [
            'code' => $fields['code'],
            'component_id' => $fields['component_id'],
            'year' => $fields['year'],
            'month' => $fields['month'],
            'employee_id' => $fields['employee_id'],
        ];

        $updatedFields = [
            'description' => $fields['description'] ?? '',
            'amount' => $fields['amount'],
        ];

        try {
            $this->payrollUploadRepository->updateOrCreatePayrollUpload($targetFields, $updatedFields);

            $arrResponse = [
                'message' => 'Data berhasil disimpan',
            ];
        } catch (Exception $e) {
            $arrResponse = [
                'message' => 'Data gagal disimpan, '.$e->getMessage(),
            ];
        }

        return $arrResponse;
    }

    public function deletePayrollUpload(int $id): void
    {
        try {
            $this->payrollUploadRepository->deletePayrollUpload($id);

            Alert::success('Berhasil', 'Data berhasil dihapus');
        } catch (Exception $e) {

            Alert::error('Gagal', 'Data gagal dihapus '.$e->getMessage());
        }
    }

    public function processImportPayrollUpload(Request $request): array
    {
        $code = getParameterMenu($this->menu_path());
        $componentId = PayrollComponent::whereCode($code)->first()->id;
        try {
            if($request->hasFile('filename')) {
                Excel::import(new UploadImport(
                    $code,
                    $componentId
                ), $request->file('filename'));

                $arrResponse = [
                    'message' => 'Data berhasil disimpan',
                ];
            }else{
                $arrResponse = [
                    'message' => 'Gagal, file tidak ditemukan',
                ];
            }
        } catch (Exception $e) {
            $arrResponse = [
                'message' => 'Gagal, data gagal disimpan '.$e->getMessage(),
            ];
        }

        return $arrResponse;
    }

}
