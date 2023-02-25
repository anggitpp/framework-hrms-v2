<?php

namespace App\Repositories\Employee;

use App\Models\Employee\EmployeeWork;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class EmployeeWorkRepository extends BaseRepository
{
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function getWorks(): Builder
    {
        return EmployeeWork::query()->join('employees', 'employee_works.employee_id', 'employees.id')->join('employee_positions', function ($join) {
            $join->on('employee_works.employee_id', '=', 'employee_positions.employee_id')
                ->where('employee_positions.status', 't');
        });
    }
}
