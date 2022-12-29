<?php

namespace App\Models\Employee;

use App\Models\Setting\AppMasterData;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeTraining extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'employee_id',
        'certificate_number',
        'subject',
        'institution',
        'category_id',
        'type_id',
        'start_date',
        'end_date',
        'location',
        'description',
        'filename',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class);
    }
}
