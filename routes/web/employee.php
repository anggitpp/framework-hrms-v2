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

    Route::get('/employees/employees/training/{id}/create', [EmployeeController::class, 'trainingCreate'])->name('employees.training.create');
    Route::post('/employees/employees/training/{id}', [EmployeeController::class, 'trainingStore'])->name('employees.training.store');
    Route::get('/employees/employees/training/{id}/edit', [EmployeeController::class, 'trainingEdit'])->name('employees.training.edit');
    Route::patch('/employees/employees/training/{id}', [EmployeeController::class, 'trainingUpdate'])->name('employees.training.update');
    Route::delete('/employees/employees/training/{id}', [EmployeeController::class, 'trainingDestroy'])->name('employees.training.destroy');

    Route::get('/employees/employees/work/{id}/create', [EmployeeController::class, 'workCreate'])->name('employees.work.create');
    Route::post('/employees/employees/work/{id}', [EmployeeController::class, 'workStore'])->name('employees.work.store');
    Route::get('/employees/employees/work/{id}/edit', [EmployeeController::class, 'workEdit'])->name('employees.work.edit');
    Route::patch('/employees/employees/work/{id}', [EmployeeController::class, 'workUpdate'])->name('employees.work.update');
    Route::delete('/employees/employees/work/{id}', [EmployeeController::class, 'workDestroy'])->name('employees.work.destroy');

    Route::get('/employees/employees/asset/{id}/create', [EmployeeController::class, 'assetCreate'])->name('employees.asset.create');
    Route::post('/employees/employees/asset/{id}', [EmployeeController::class, 'assetStore'])->name('employees.asset.store');
    Route::get('/employees/employees/asset/{id}/edit', [EmployeeController::class, 'assetEdit'])->name('employees.asset.edit');
    Route::patch('/employees/employees/asset/{id}', [EmployeeController::class, 'assetUpdate'])->name('employees.asset.update');
    Route::delete('/employees/employees/asset/{id}', [EmployeeController::class, 'assetDestroy'])->name('employees.asset.destroy');

    Route::get('/employees/employees/file/{id}/create', [EmployeeController::class, 'fileCreate'])->name('employees.file.create');
    Route::post('/employees/employees/file/{id}', [EmployeeController::class, 'fileStore'])->name('employees.file.store');
    Route::get('/employees/employees/file/{id}/edit', [EmployeeController::class, 'fileEdit'])->name('employees.file.edit');
    Route::patch('/employees/employees/file/{id}', [EmployeeController::class, 'fileUpdate'])->name('employees.file.update');
    Route::delete('/employees/employees/file/{id}', [EmployeeController::class, 'fileDestroy'])->name('employees.file.destroy');

    Route::resource('/employees/setting-unit-structures', EmployeeUnitStructureController::class);

    Route::resource('/employees/position-histories', EmployeePositionHistoryController::class);
    Route::get('/employees/position-histories/subMasters/{id}', [EmployeePositionHistoryController::class, 'subMasters'])->name('position-histories.subMasters');

    Route::resource('/employees/signatures', EmployeeSignatureSettingController::class);
});


