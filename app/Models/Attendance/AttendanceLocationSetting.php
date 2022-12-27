<?php

namespace App\Models\Attendance;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLocationSetting extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'location_id',
        'address',
        'wfh',
        'latitude',
        'longitude',
        'radius',
    ];
}
