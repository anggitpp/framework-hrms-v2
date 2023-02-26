<?php

namespace App\Repositories\Employee;

use App\Models\Employee\EmployeePosition;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class EmployeePositionRepository extends BaseRepository
{
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function getPositions(): Builder
    {
        return EmployeePosition::query()->join('employees', 'employee_positions.employee_id', 'employees.id');
    }
}
