<?php

namespace App\Models\Payroll;

use App\Models\Employee\Employee;
use App\Models\Employee\EmployeePosition;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollUpload extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'code',
        'month',
        'year',
        'component_id',
        'employee_id',
        'amount',
        'description',
    ];

    public function component(): BelongsTo
    {
        return $this->belongsTo(PayrollComponent::class, 'component_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(EmployeePosition::class, 'employee_id', 'employee_id')->where('status', 't');
    }
}
