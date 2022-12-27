<?php

namespace App\Models\Attendance;

use App\Models\Employee\Employee;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLeave extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'employee_id',
        'leave_master_id',
        'number',
        'date',
        'start_date',
        'end_date',
        'balance',
        'amount',
        'remaining',
        'description',
        'filename',
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

    /**
     * @return BelongsTo
     */
    public function leaveMaster(): BelongsTo
    {
        return $this->belongsTo(AttendanceLeaveMaster::class);
    }

}
