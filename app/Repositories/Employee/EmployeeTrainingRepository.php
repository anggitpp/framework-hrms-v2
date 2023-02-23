<?php

namespace App\Repositories\Employee;

use App\Models\Employee\EmployeeTraining;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class EmployeeTrainingRepository extends BaseRepository
{
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function getTrainings(): Builder
    {
        return EmployeeTraining::query()->join('employees', 'employee_trainings.employee_id', 'employees.id')->join('employee_positions', function ($join) {
            $join->on('employee_trainings.employee_id', '=', 'employee_positions.employee_id')
                ->where('employee_positions.status', 't');
        });
    }
}
