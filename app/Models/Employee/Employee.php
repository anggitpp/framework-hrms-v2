<?php

namespace App\Models\Employee;

use App\Models\Setting\AppParameter;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'name',
        'nickname',
        'date_of_birth',
        'place_of_birth',
        'employee_number',
        'identity_number',
        'identity_address',
        'address',
        'religion_id',
        'status_id',
        'photo',
        'identity_file',
        'join_date',
        'leave_date',
        'phone_number',
        'mobile_phone_number',
        'marital_status_id',
        'email',
        'gender',
        'attendance_pin',
    ];

    public function position(): HasOne
    {
        $position = $this->hasOne(EmployeePosition::class);
        $position->getQuery()->where('status', 't');

        return $position;
    }

    public function positions(): HasMany
    {
        return $this->hasMany(EmployeePosition::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status_id', AppParameter::whereCode('SAP')->value('value'));
    }
}
