<?php

namespace App\Models\Payroll;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollFixed extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'code',
        'name',
        'amount',
        'description',
        'status',
    ];
}
