<?php

namespace App\Models\Attendance;

use App\Models\Employee\Employee;
use App\Models\Setting\AppMasterData;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendancePermission extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'number',
        'employee_id',
        'category_id',
        'date',
        'start_date',
        'end_date',
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
    public function category(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class)->where('app_master_category_code', 'AKI');
    }

    /**
     * @return BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
