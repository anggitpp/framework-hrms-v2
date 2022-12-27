<?php

namespace App\Models\Attendance;

use App\Models\Employee\Employee;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceCorrection extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'number',
        'employee_id',
        'date',
        'attendance_date',
        'start_time',
        'end_time',
        'duration',
        'actual_start_time',
        'actual_end_time',
        'actual_duration',
        'description',
        'approved_by',
        'approved_status',
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
