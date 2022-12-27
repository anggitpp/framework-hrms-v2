<?php

namespace App\Models\Attendance;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceWorkSchedule extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'employee_id',
        'shift_id',
        'location_id',
        'date',
        'start_time',
        'end_time',
        'description',
    ];
}
