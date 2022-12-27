<?php

namespace App\Models\Attendance;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceHoliday extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'description',
        'status',
    ];
}
