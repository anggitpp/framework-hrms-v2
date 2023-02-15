<?php

namespace App\Repositories\Employee;

use App\Models\Employee\EmployeeFamily;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class EmployeeFamilyRepository extends BaseRepository
{
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function getFamilies(): Builder
    {
        return EmployeeFamily::query()->join('employees', 'employee_families.employee_id', 'employees.id')->join('employee_positions', function ($join) {
            $join->on('employee_families.employee_id', '=', 'employee_positions.employee_id')
                ->where('employee_positions.status', 't');
        });
    }
}
