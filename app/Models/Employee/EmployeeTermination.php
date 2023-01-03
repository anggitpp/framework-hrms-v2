<?php

namespace App\Models\Employee;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeTermination extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'number',
        'employee_id',
        'reason_id',
        'type_id',
        'date',
        'description',
        'filename',
        'effective_date',
        'note',
        'approved_by',
        'approved_status',
        'approved_date',
        'approved_note',
    ];
}
