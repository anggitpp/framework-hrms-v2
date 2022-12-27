<?php

namespace App\Models\ESS;

use App\Models\Employee\Employee;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EssTimesheet extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'employee_id',
        'activity',
        'output',
        'date',
        'start_time',
        'end_time',
        'duration',
        'volume',
        'type',
        'description',
    ];

    /**
     * @return BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
