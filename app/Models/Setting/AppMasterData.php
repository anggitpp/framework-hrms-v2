<?php

namespace App\Models\Setting;

use App\Models\Payroll\PayrollSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(AppMasterData::class, 'parent_id');
    }

    public function payrollSetting(): HasOne
    {
        return $this->hasOne(PayrollSetting::class, 'reference_id');
    }
}
