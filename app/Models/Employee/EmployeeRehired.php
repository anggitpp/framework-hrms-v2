<?php

namespace App\Models\Employee;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeRehired extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'number',
        'employee_id',
        'date',
        'description',
        'filename',
        'effective_date',
        'approved_by',
        'approved_status',
        'approved_date',
        'approved_note',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
