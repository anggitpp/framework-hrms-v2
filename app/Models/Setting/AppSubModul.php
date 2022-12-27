<?php

namespace App\Models\Setting;

use App\Models\Setting\AppMenu;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSubModul extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_modul_id',
        'name',
        'description',
        'order',
        'status',
    ];

    public function appModul()
    {
        return $this->belongsTo(AppModul::class);
    }

    public function appMenu()
    {
        return $this->hasMany(AppMenu::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 't');
    }
}
