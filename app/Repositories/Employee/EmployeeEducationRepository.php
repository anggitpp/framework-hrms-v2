<?php

namespace App\Repositories\Employee;

use App\Models\Employee\EmployeeEducation;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class EmployeeEducationRepository extends BaseRepository
{
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function getEducation(): Builder
    {
        return EmployeeEducation::query()->join('employees', 'employee_education.employee_id', 'employees.id')->join('employee_positions', function ($join) {
            $join->on('employee_education.employee_id', '=', 'employee_positions.employee_id')
                ->where('employee_positions.status', 't');
        });
    }
}
