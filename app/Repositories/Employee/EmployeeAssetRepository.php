<?php

namespace App\Repositories\Employee;

use App\Models\Employee\EmployeeAsset;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class EmployeeAssetRepository extends BaseRepository
{
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function getAssets(): Builder
    {
        return EmployeeAsset::query()->join('employees', 'employee_assets.employee_id', 'employees.id')->join('employee_positions', function ($join) {
            $join->on('employee_assets.employee_id', '=', 'employee_positions.employee_id')
                ->where('employee_positions.status', 't');
        });
    }
}
