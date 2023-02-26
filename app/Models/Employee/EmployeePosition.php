<?php

namespace App\Models\Employee;

use App\Models\Attendance\AttendanceShift;
use App\Models\Setting\AppMasterData;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 't');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class);
    }

    public function rank(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(AttendanceShift::class);
    }

    public function employeeType(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class);
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
