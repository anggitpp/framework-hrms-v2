<?php

namespace App\Models\Attendance;

use App\Models\Setting\AppMasterData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceMachineSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'serial_number',
        'name',
        'ip_address',
        'address',
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
