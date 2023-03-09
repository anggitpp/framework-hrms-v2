<?php

namespace App\Models\Employee;

use App\Models\Setting\AppMasterData;
use App\Models\Setting\AppParameter;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'npwp_number',
        'npwp_date',
        'bpjs_date',
        'bpjs_number',
        'bpjs_tk_number',
        'bpjs_tk_date',
        'blood_type',
    ];

    public function position(): HasOne
    {
        $position = $this->hasOne(EmployeePosition::class);
        $position->getQuery()->where('status', 't');

        return $position;
    }

    public function bank(): HasOne
    {
        return $this->hasOne(EmployeeBank::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(EmployeePosition::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status_id', AppParameter::whereCode('SAP')->value('value'));
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class, 'status_id');
    }

    public function maritalStatus(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class, 'marital_status_id');
    }

    public function religion(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class, 'religion_id');
    }
}
