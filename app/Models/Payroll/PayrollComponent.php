<?php

namespace App\Models\Payroll;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollComponent extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'master_id',
        'type',
        'code',
        'name',
        'description',
        'status',
        'calculation_type',
        'calculation_cut_off',
        'calculation_cut_off_date_start',
        'calculation_cut_off_date_end',
        'calculation_description',
        'calculation_amount',
        'calculation_amount_min',
        'calculation_amount_max',
    ];
}
