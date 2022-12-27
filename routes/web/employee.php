<?php

use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\EmployeePositionHistoryController;
use App\Http\Controllers\Employee\EmployeeSignatureSettingController;
use App\Http\Controllers\Employee\EmployeeUnitStructureController;
use Illuminate\Support\Facades\Route;

Route::name('employees.')->group(function () {
    Route::resource('/employees/employees', EmployeeController::class);

    Route::resource('/employees/setting-unit-structures', EmployeeUnitStructureController::class);

    Route::resource('/employees/position-histories', EmployeePositionHistoryController::class);
    Route::get('/employees/position-histories/subMasters/{id}', [EmployeePositionHistoryController::class, 'subMasters'])->name('position-histories.subMasters');

    Route::resource('/employees/signatures', EmployeeSignatureSettingController::class);
});


