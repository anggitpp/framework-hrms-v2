<?php

namespace App\Models\Employee;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeWork extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'employee_id',
        'company',
        'position',
        'start_date',
        'end_date',
        'city',
        'job_desc',
        'description',
        'filename',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
