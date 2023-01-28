<?php

namespace App\Services\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Payroll\PayrollSetting;
use App\Models\Setting\AppMasterData;
use App\Repositories\Payroll\PayrollSettingRepository;
use App\Repositories\Setting\AppMasterDataRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayrollSettingService extends Controller
{

    private PayrollSettingRepository $payrollSettingRepository;
    private AppMasterDataRepository $appMasterDataRepository;
    public function __construct()
    {
        $this->payrollSettingRepository = new PayrollSettingRepository();
        $this->appMasterDataRepository = new AppMasterDataRepository();
    }

    public function getPayrollSettings() : Collection
    {
        return $this->payrollSettingRepository->getPayrollSettings()->get();
    }

    public function getPayrollSettingById(int $id) : PayrollSetting
    {
        return $this->payrollSettingRepository->getPayrollSettingById($id);
    }

    public function getPayrollSettingFromMasterData(string $code, int $id) : AppMasterData
    {
        return $this->payrollSettingRepository->getMasterDataWithPayrollSettingById($code, $id);
    }

    /**
     * @throws Exception
     */
    public function dataPayrollSettings(Request $request): JsonResponse
    {
        list($code, $codeMaster, $field) = explode(' - ', getParameterMenu($this->menu_path()));

        if($request->ajax()) {
            $query = $this->appMasterDataRepository->getMasters($codeMaster)
                ->select(['id', 'name'])
                ->with('payrollSetting', function ($query) use ($code) {
                    $query->where('code', $code);
                });
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) $query->where('name', 'like', "%{$filter}%");
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'amount', 'type' => 'master_relationship', 'masters' => 'payrollSetting', 'master_field' => 'amount', 'master_type_value' => 'currency'],
            ], true);
        }
    }

    public function savePayrollSetting(Request $request, int $id): array
    {
        list($code, $codeMaster, $field) = explode(' - ', getParameterMenu($this->menu_path()));

        $fields = [
            'code' => $code,
            'reference_field' => $field,
            'reference_id' => $id,
            'amount' => resetCurrency($request->input('amount')),
        ];

        try {
            $this->payrollSettingRepository->updatePayrollSetting($fields);

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
}
