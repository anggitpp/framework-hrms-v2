<?php

namespace App\Models\Employee;

use App\Models\Setting\AppMasterData;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAsset extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'employee_id',
        'name',
        'number',
        'date',
        'category_id',
        'type_id',
        'start_date',
        'end_date',
        'description',
        'filename',
        'status',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class);
    }

    /**
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class);
    }
}
