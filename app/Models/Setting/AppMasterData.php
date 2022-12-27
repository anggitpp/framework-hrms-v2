<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppMasterData extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'app_master_category_code',
        'name',
        'code',
        'description',
        'order',
        'status',
    ];
}
