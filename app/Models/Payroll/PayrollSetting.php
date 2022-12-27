<?php

namespace App\Models\Payroll;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollSetting extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'code',
        'reference_field',
        'reference_id',
        'amount',
    ];
}
