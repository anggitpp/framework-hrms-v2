<?php

namespace App\Repositories\Employee;

use App\Models\Employee\EmployeeBank;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class EmployeeBankRepository extends BaseRepository
{
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function getBanks(): Builder
    {
        return EmployeeBank::query()->join('employees', 'employee_banks.employee_id', 'employees.id')->join('employee_positions', function ($join) {
            $join->on('employee_banks.employee_id', '=', 'employee_positions.employee_id')
                ->where('employee_positions.status', 't');
        });
    }
}
