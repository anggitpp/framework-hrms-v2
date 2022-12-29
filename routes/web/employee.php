<?php

use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\EmployeePositionHistoryController;
use App\Http\Controllers\Employee\EmployeeSignatureSettingController;
use App\Http\Controllers\Employee\EmployeeUnitStructureController;
use Illuminate\Support\Facades\Route;

Route::name('employees.')->group(function () {
    Route::resource('/employees/employees', EmployeeController::class);
    Route::get('/employees/employees/subMasters/{id}', [EmployeeController::class, 'subMasters'])->name('employees.subMasters');

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

    Route::get('/employees/employees/contact/{id}/create', [EmployeeController::class, 'contactCreate'])->name('employees.contact.create');
    Route::post('/employees/employees/contact/{id}', [EmployeeController::class, 'contactStore'])->name('employees.contact.store');
    Route::get('/employees/employees/contact/{id}/edit', [EmployeeController::class, 'contactEdit'])->name('employees.contact.edit');
    Route::patch('/employees/employees/contact/{id}', [EmployeeController::class, 'contactUpdate'])->name('employees.contact.update');
    Route::delete('/employees/employees/contact/{id}', [EmployeeController::class, 'contactDestroy'])->name('employees.contact.destroy');

    Route::get('/employees/employees/position/{id}/create', [EmployeeController::class, 'positionCreate'])->name('employees.position.create');
    Route::post('/employees/employees/position/{id}', [EmployeeController::class, 'positionStore'])->name('employees.position.store');
    Route::get('/employees/employees/position/{id}/edit', [EmployeeController::class, 'positionEdit'])->name('employees.position.edit');
    Route::patch('/employees/employees/position/{id}', [EmployeeController::class, 'positionUpdate'])->name('employees.position.update');
    Route::delete('/employees/employees/position/{id}', [EmployeeController::class, 'positionDestroy'])->name('employees.position.destroy');
    Route::get('/employees/employees/position/subMasters/{id}', [EmployeeController::class, 'subMasters'])->name('employees.position.subMasters');


    Route::resource('/employees/setting-unit-structures', EmployeeUnitStructureController::class);

    Route::resource('/employees/position-histories', EmployeePositionHistoryController::class);
    Route::get('/employees/position-histories/subMasters/{id}', [EmployeePositionHistoryController::class, 'subMasters'])->name('position-histories.subMasters');

    Route::resource('/employees/signatures', EmployeeSignatureSettingController::class);
});


