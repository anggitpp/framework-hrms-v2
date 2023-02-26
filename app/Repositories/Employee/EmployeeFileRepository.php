<?php

namespace App\Repositories\Employee;

use App\Models\Employee\EmployeeFile;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class EmployeeFileRepository extends BaseRepository
{
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function getFiles(): Builder
    {
        return EmployeeFile::query()->join('employees', 'employee_files.employee_id', 'employees.id')->join('employee_positions', function ($join) {
            $join->on('employee_files.employee_id', '=', 'employee_positions.employee_id')
                ->where('employee_positions.status', 't');
        });
    }
}
