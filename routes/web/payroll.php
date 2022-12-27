<?php

use App\Http\Controllers\Payroll\PayrollComponentController;
use App\Http\Controllers\Payroll\PayrollMasterController;
use App\Http\Controllers\Payroll\PayrollRecapController;
use App\Http\Controllers\Payroll\PayrollSettingController;
use Illuminate\Support\Facades\Route;

Route::name('payrolls.')->group(function () {
    Route::resource('/payrolls/masters', PayrollMasterController::class);

    Route::get('/payrolls/allowances/data', [PayrollComponentController::class, 'data'])->name('allowances.data');
    Route::resource('/payrolls/allowances', PayrollComponentController::class);

    Route::get('/payrolls/deductions/data', [PayrollComponentController::class, 'data'])->name('deductions.data');
    Route::resource('/payrolls/deductions', PayrollComponentController::class);

    Route::resource('/payrolls/setting-ranks', PayrollSettingController::class);

    Route::get('/payrolls/payroll-recap', [PayrollRecapController::class, 'index'])->name('payroll-recap.index');
    Route::get('/payrolls/payroll-recap/data', [PayrollRecapController::class, 'data'])->name('payroll-recap.data');
    Route::get('/payrolls/payroll-recap/export', [PayrollRecapController::class, 'export'])->name('payroll-recap.export');
    Route::get('/payrolls/payroll-recap/pdf', [PayrollRecapController::class, 'pdf'])->name('payroll-recap.pdf');
});
