<?php

use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\EmployeePositionHistoryController;
use App\Http\Controllers\Employee\EmployeeSignatureSettingController;
use App\Http\Controllers\Employee\EmployeeUnitStructureController;
use Illuminate\Support\Facades\Route;

Route::name('employees.')->group(function () {
    Route::resource('/employees/employees', EmployeeController::class);

    Route::get('/employees/employees/families/{id}/create', [EmployeeController::class, 'familyCreate'])->name('employees.families.create');
    Route::post('/employees/employees/families/{id}', [EmployeeController::class, 'familyStore'])->name('employees.families.store');
    Route::get('/employees/employees/families/{id}/edit', [EmployeeController::class, 'familyEdit'])->name('employees.families.edit');
    Route::patch('/employees/employees/families/{id}', [EmployeeController::class, 'familyUpdate'])->name('employees.families.update');
    Route::delete('/employees/employees/families/{id}', [EmployeeController::class, 'familyDestroy'])->name('employees.families.destroy');

    Route::get('/employees/employees/education/{id}/create', [EmployeeController::class, 'educationCreate'])->name('employees.education.create');
    Route::post('/employees/employees/education/{id}', [EmployeeController::class, 'educationStore'])->name('employees.education.store');
    Route::get('/employees/employees/education/{id}/edit', [EmployeeController::class, 'educationEdit'])->name('employees.education.edit');
    Route::patch('/employees/employees/education/{id}', [EmployeeController::class, 'educationUpdate'])->name('employees.education.update');
    Route::delete('/employees/employees/education/{id}', [EmployeeController::class, 'educationDestroy'])->name('employees.education.destroy');

    Route::resource('/employees/setting-unit-structures', EmployeeUnitStructureController::class);

    Route::resource('/employees/position-histories', EmployeePositionHistoryController::class);
    Route::get('/employees/position-histories/subMasters/{id}', [EmployeePositionHistoryController::class, 'subMasters'])->name('position-histories.subMasters');

    Route::resource('/employees/signatures', EmployeeSignatureSettingController::class);
});


