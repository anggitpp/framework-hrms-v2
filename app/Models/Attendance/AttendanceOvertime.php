<?php

namespace App\Models\Attendance;

use App\Models\Employee\Employee;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceOvertime extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'number',
        'employee_id',
        'date',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'duration',
        'description',
        'filename',
        'approved_status',
        'approved_by',
        'approved_date',
        'approved_note',
    ];

    /**
     * @return BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
