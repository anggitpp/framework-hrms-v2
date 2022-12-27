<?php

namespace App\Models\Employee;

use App\Models\Setting\AppMasterData;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSignatureSetting extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'location_id',
        'employee_id',
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

    /**
     * @return BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeActive()
    {
        return $this->where('status', 't');
    }
}
