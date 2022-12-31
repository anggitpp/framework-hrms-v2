<?php

namespace App\Models\Attendance;

use App\Models\Setting\AppMasterData;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceShift extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'location_id',
        'name',
        'code',
        'start',
        'end',
        'night_shift',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class)->where('app_master_category_code', 'ELK');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 't');
    }
}
