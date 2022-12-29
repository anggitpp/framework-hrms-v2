<?php

namespace App\Models\Employee;

use App\Models\Setting\AppMasterData;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeContact extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'employee_id',
        'relationship_id',
        'name',
        'phone_number',
        'description',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function relationship(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class);
    }
}
