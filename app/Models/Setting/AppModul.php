<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppModul extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'target',
        'icon',
        'description',
        'order',
        'status',
    ];

    public function subModul()
    {
        return $this->hasMany(AppSubModul::class);
    }

    public function menus()
    {
        return $this->hasMany(AppMenu::class);
    }

    public function scopeActive($query, $id = "")
    {
        $query->where('status', 't');
        if($id)  $query->orWhere('id', $id);

        return $query;
    }
}
