<?php

namespace App\Models\Employee;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePosition extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'employee_id',
        'position_id',
        'start_date',
        'end_date',
        'rank_id',
        'grade_id',
        'location_id',
        'shift_id',
        'employee_type_id',
        'sk_date',
        'sk_number',
        'unit_id',
        'leader_id',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 't');
    }
}
