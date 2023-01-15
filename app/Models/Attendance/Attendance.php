<?php

namespace App\Models\Attendance;

use App\Models\Employee\Employee;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'employee_id',
        'location_id',
        'type',
        'type_id',
        'start_date',
        'start_time',
        'start_latitude',
        'start_longitude',
        'start_image',
        'start_address',
        'end_date',
        'end_time',
        'end_latitude',
        'end_longitude',
        'end_image',
        'end_address',
        'duration',
        'description',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
