<?php

namespace App\Models\Employee;

use App\Models\Setting\AppMasterData;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeBank extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'employee_id',
        'bank_id',
        'account_number',
        'account_name',
        'branch',
        'description',
        'status',

    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class);
    }
}
