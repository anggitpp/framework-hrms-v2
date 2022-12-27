<?php

namespace App\Models\Employee;

use App\Models\Setting\AppMasterData;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeUnitStructure extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'unit_id',
        'leader_id',
        'administration_id',
    ];

    public function unit()
    {
        return $this->belongsTo(AppMasterData::class, 'unit_id');
    }

    public function leader()
    {
        return $this->belongsTo(Employee::class, 'leader_id');
    }

    public function administration()
    {
        return $this->belongsTo(Employee::class, 'administration_id');
    }
}
