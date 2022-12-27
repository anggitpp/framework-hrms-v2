<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'app_modul_id',
        'app_sub_modul_id',
        'name',
        'target',
        'description',
        'icon',
        'parameter',
        'full_screen',
        'order',
        'status',
    ];

    public function parent()
    {
        return $this->belongsTo(AppMenu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(AppMenu::class, 'parent_id');
    }

    public function modul()
    {
        return $this->belongsTo(AppModul::class, 'app_modul_id');
    }

    public function subModul()
    {
        return $this->belongsTo(AppSubModul::class, 'app_sub_modul_id');
    }

    public function scopeActive($query, $id = "")
    {
        $query->where('status', 't');
        if($id)  $query->orWhere('id', $id);

        return $query;
    }
}
