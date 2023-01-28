<?php

namespace App\Repositories\Payroll;

use App\Models\Payroll\PayrollSetting;
use App\Models\Setting\AppMasterData;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PayrollSettingRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new PayrollSetting());
    }
    public function getPayrollSettings(): Builder
    {
        return $this->query();
    }

    public function getPayrollSettingById(int $id): PayrollSetting
    {
        return $this->getById($id);
    }

    public function getMasterDataWithPayrollSettings(string $masterCode, string $settingCode): Collection
    {
        return AppMasterData::with(['payrollSetting' => function ($query) use ($settingCode) {
            $query->where('code', $settingCode);
        }])
            ->whereAppMasterCategoryCode($masterCode)
            ->get();
    }

    public function getMasterDataWithPayrollSettingById(string $code, int $id): AppMasterData
    {
        return AppMasterData::select(['id', 'name'])->with(['payrollSetting' => function ($query) use ($code) {
            $query->where('code', $code);
        }])
            ->whereId($id)
            ->first();
    }

    public function updatePayrollSetting(array $data): PayrollSetting
    {
        return $this->updateOrCreate([
            'code' => $data['code'],
            'reference_field' => $data['reference_field'],
            'reference_id' => $data['reference_id'],
        ],
        [
            'amount' => $data['amount'],
        ]);

    }
}
