<?php

namespace App\Models\Employee;

use App\Models\Setting\AppMasterData;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeEducation extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'employee_id',
        'level_id',
        'name',
        'major',
        'essay',
        'city',
        'score',
        'start_year',
        'end_year',
        'description',
        'filename',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class, 'level_id');
    }
}
