<?php

namespace App\Models\Employee;

use App\Models\Setting\AppMasterData;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeTermination extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'number',
        'employee_id',
        'reason_id',
        'type_id',
        'date',
        'description',
        'filename',
        'effective_date',
        'note',
        'approved_by',
        'approved_status',
        'approved_date',
        'approved_note',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reason(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class, 'reason_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class, 'type_id');
    }
}
