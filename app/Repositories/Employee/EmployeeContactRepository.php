<?php

namespace App\Repositories\Employee;

use App\Models\Employee\EmployeeContact;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class EmployeeContactRepository extends BaseRepository
{
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function getContacts(): Builder
    {
        return EmployeeContact::query()->join('employees', 'employee_contacts.employee_id', 'employees.id')->join('employee_positions', function ($join) {
            $join->on('employee_contacts.employee_id', '=', 'employee_positions.employee_id')
                ->where('employee_positions.status', 't');
        });
    }
}
