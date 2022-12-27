<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'value',
        'description',
    ];
}
