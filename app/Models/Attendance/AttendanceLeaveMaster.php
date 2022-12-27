<?php

namespace App\Models\Attendance;

use App\Models\Setting\AppMasterData;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLeaveMaster extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'name',
        'location_id',
        'balance',
        'start_date',
        'end_date',
        'work_period',
        'gender',
        'description',
        'status',
    ];

    /**
     * @return BelongsTo
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class)->where('app_master_category_code', 'ELK');
    }
}
