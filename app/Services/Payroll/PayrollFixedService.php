<?php

namespace App\Services\Payroll;

use Alert;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\PayrollFixedRequest;
use App\Models\Payroll\PayrollFixed;
use App\Repositories\Payroll\PayrollFixedRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayrollFixedService extends Controller
{
    private PayrollFixedRepository $payrollFixedRepository;
    public function __construct()
    {
        $this->payrollFixedRepository = new PayrollFixedRepository();
    }

    public function getPayrollFixeds() : Builder
    {
        return $this->payrollFixedRepository->getPayrollFixeds();
    }

    public function getPayrollFixedById(int $id) : PayrollFixed
    {
        return $this->payrollFixedRepository->getPayrollFixedById($id);
    }

    /**
     * @throws Exception
     */
    public function dataPayrollFixeds(Request $request): JsonResponse
    {
        if($request->ajax()) {
            $query = $this->getPayrollFixeds();
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) $query->where('name', 'like', "%{$filter}%")->orWhere('code', 'like', "%{$filter}%");
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'amount', 'type' => 'currency'],
                ['name' => 'status', 'type' => 'status'],
            ], true);
        }
    }

    public function savePayrollFixed(PayrollFixedRequest $request, int $id = 0): array
    {
        $fields = [
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'amount' => resetCurrency($request->input('amount')),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ];

        try {
            if($id == 0){
                $this->payrollFixedRepository->storePayrollFixed($fields);
            }else {
                $this->payrollFixedRepository->updatePayrollFixed($fields, $id);
            }

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

    public function deletePayrollFixed(int $id): void
    {
        try {
            $this->payrollFixedRepository->deletePayrollFixed($id);

            Alert::success('Berhasil', 'Data berhasil dihapus');
        } catch (Exception $e) {

            Alert::error('Gagal', 'Data gagal dihapus '.$e->getMessage());
        }
    }
}
