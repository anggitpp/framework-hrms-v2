<?php

use App\Http\Controllers\Payroll\PayrollComponentController;
use App\Http\Controllers\Payroll\PayrollMasterController;
use App\Http\Controllers\Payroll\PayrollRecapController;
use App\Http\Controllers\Payroll\PayrollSettingController;
use App\Http\Controllers\Payroll\PayrollUploadController;
use Illuminate\Support\Facades\Route;

Route::name('payrolls.')->group(function () {
    Route::resource('/payrolls/masters', PayrollMasterController::class);

    Route::get('/payrolls/allowances/data', [PayrollComponentController::class, 'data'])->name('allowances.data');
    Route::resource('/payrolls/allowances', PayrollComponentController::class);

    Route::get('/payrolls/deductions/data', [PayrollComponentController::class, 'data'])->name('deductions.data');
    Route::resource('/payrolls/deductions', PayrollComponentController::class);

    Route::get('/payrolls/setting-ranks/data', [PayrollSettingController::class, 'data'])->name('setting-ranks.data');
    Route::resource('/payrolls/setting-ranks', PayrollSettingController::class);

    Route::get('/payrolls/setting-salaries/data', [PayrollSettingController::class, 'data'])->name('setting-salaries.data');
    Route::resource('/payrolls/setting-salaries', PayrollSettingController::class);

    Route::get('/payrolls/upload-overtimes', [PayrollUploadController::class, 'index'])->name('upload-overtimes.index');
    Route::get('/payrolls/upload-overtimes/detail/{month}/{year}', [PayrollUploadController::class, 'detail'])->name('upload-overtimes.detail');
    Route::get('/payrolls/upload-overtimes/data-detail/{month}/{year}', [PayrollUploadController::class, 'dataDetail'])->name('upload-overtimes.data-detail');
    Route::get('/payrolls/upload-overtimes/import', [PayrollUploadController::class, 'import'])->name('upload-overtimes.import');
    Route::patch('/payrolls/upload-overtimes/process-import', [PayrollUploadController::class, 'processImport'])->name('upload-overtimes.process-import');
    Route::get('/payrolls/upload-overtimes/edit/{id}', [PayrollUploadController::class, 'edit'])->name('upload-overtimes.edit');
    Route::patch('/payrolls/upload-overtimes/update/{id}', [PayrollUploadController::class, 'update'])->name('upload-overtimes.update');
    Route::delete('/payrolls/upload-overtimes/destroy/{id}', [PayrollUploadController::class, 'destroy'])->name('upload-overtimes.destroy');

    Route::get('/payrolls/payroll-recap', [PayrollRecapController::class, 'index'])->name('payroll-recap.index');
    Route::get('/payrolls/payroll-recap/data', [PayrollRecapController::class, 'data'])->name('payroll-recap.data');
    Route::get('/payrolls/payroll-recap/export', [PayrollRecapController::class, 'export'])->name('payroll-recap.export');
    Route::get('/payrolls/payroll-recap/pdf', [PayrollRecapController::class, 'pdf'])->name('payroll-recap.pdf');
});
