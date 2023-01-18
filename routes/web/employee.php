<?php

use App\Http\Controllers\Employee\EmployeeAssetController;
use App\Http\Controllers\Employee\EmployeeContactController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\EmployeeEducationController;
use App\Http\Controllers\Employee\EmployeeFamilyController;
use App\Http\Controllers\Employee\EmployeeFileController;
use App\Http\Controllers\Employee\EmployeePositionHistoryController;
use App\Http\Controllers\Employee\EmployeeRehiredController;
use App\Http\Controllers\Employee\EmployeeSignatureSettingController;
use App\Http\Controllers\Employee\EmployeeTerminationController;
use App\Http\Controllers\Employee\EmployeeTrainingController;
use App\Http\Controllers\Employee\EmployeeUnitStructureController;
use App\Http\Controllers\Employee\EmployeeWorkController;
use Illuminate\Support\Facades\Route;

Route::name('employees.')->group(function () {
    Route::get('/employees/employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::get('/employees/employees/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::patch('/employees/employees/process-import', [EmployeeController::class, 'processImport'])->name('employees.process-import');
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

    Route::resource('/employees/employees-nonactive', EmployeeController::class);

    Route::get('/employees/employees-nonactive/families/{id}/create', [EmployeeController::class, 'familyCreate'])->name('employees-nonactive.families.create');
    Route::post('/employees/employees-nonactive/families/{id}', [EmployeeController::class, 'familyStore'])->name('employees-nonactive.families.store');
    Route::get('/employees/employees-nonactive/families/{id}/edit', [EmployeeController::class, 'familyEdit'])->name('employees-nonactive.families.edit');
    Route::patch('/employees/employees-nonactive/families/{id}', [EmployeeController::class, 'familyUpdate'])->name('employees-nonactive.families.update');
    Route::delete('/employees/employees-nonactive/families/{id}', [EmployeeController::class, 'familyDestroy'])->name('employees-nonactive.families.destroy');

    Route::get('/employees/employees-nonactive/education/{id}/create', [EmployeeController::class, 'educationCreate'])->name('employees-nonactive.education.create');
    Route::post('/employees/employees-nonactive/education/{id}', [EmployeeController::class, 'educationStore'])->name('employees-nonactive.education.store');
    Route::get('/employees/employees-nonactive/education/{id}/edit', [EmployeeController::class, 'educationEdit'])->name('employees-nonactive.education.edit');
    Route::patch('/employees/employees-nonactive/education/{id}', [EmployeeController::class, 'educationUpdate'])->name('employees-nonactive.education.update');
    Route::delete('/employees/employees-nonactive/education/{id}', [EmployeeController::class, 'educationDestroy'])->name('employees-nonactive.education.destroy');

    Route::get('/employees/employees-nonactive/contact/{id}/create', [EmployeeController::class, 'contactCreate'])->name('employees-nonactive.contact.create');
    Route::post('/employees/employees-nonactive/contact/{id}', [EmployeeController::class, 'contactStore'])->name('employees-nonactive.contact.store');
    Route::get('/employees/employees-nonactive/contact/{id}/edit', [EmployeeController::class, 'contactEdit'])->name('employees-nonactive.contact.edit');
    Route::patch('/employees/employees-nonactive/contact/{id}', [EmployeeController::class, 'contactUpdate'])->name('employees-nonactive.contact.update');
    Route::delete('/employees/employees-nonactive/contact/{id}', [EmployeeController::class, 'contactDestroy'])->name('employees-nonactive.contact.destroy');

    Route::get('/employees/employees-nonactive/position/{id}/create', [EmployeeController::class, 'positionCreate'])->name('employees-nonactive.position.create');
    Route::post('/employees/employees-nonactive/position/{id}', [EmployeeController::class, 'positionStore'])->name('employees-nonactive.position.store');
    Route::get('/employees/employees-nonactive/position/{id}/edit', [EmployeeController::class, 'positionEdit'])->name('employees-nonactive.position.edit');
    Route::patch('/employees/employees-nonactive/position/{id}', [EmployeeController::class, 'positionUpdate'])->name('employees-nonactive.position.update');
    Route::delete('/employees/employees-nonactive/position/{id}', [EmployeeController::class, 'positionDestroy'])->name('employees-nonactive.position.destroy');

    Route::get('/employees/employees-nonactive/training/{id}/create', [EmployeeController::class, 'trainingCreate'])->name('employees-nonactive.training.create');
    Route::post('/employees/employees-nonactive/training/{id}', [EmployeeController::class, 'trainingStore'])->name('employees-nonactive.training.store');
    Route::get('/employees/employees-nonactive/training/{id}/edit', [EmployeeController::class, 'trainingEdit'])->name('employees-nonactive.training.edit');
    Route::patch('/employees/employees-nonactive/training/{id}', [EmployeeController::class, 'trainingUpdate'])->name('employees-nonactive.training.update');
    Route::delete('/employees/employees-nonactive/training/{id}', [EmployeeController::class, 'trainingDestroy'])->name('employees-nonactive.training.destroy');

    Route::get('/employees/employees-nonactive/work/{id}/create', [EmployeeController::class, 'workCreate'])->name('employees-nonactive.work.create');
    Route::post('/employees/employees-nonactive/work/{id}', [EmployeeController::class, 'workStore'])->name('employees-nonactive.work.store');
    Route::get('/employees/employees-nonactive/work/{id}/edit', [EmployeeController::class, 'workEdit'])->name('employees-nonactive.work.edit');
    Route::patch('/employees/employees-nonactive/work/{id}', [EmployeeController::class, 'workUpdate'])->name('employees-nonactive.work.update');
    Route::delete('/employees/employees-nonactive/work/{id}', [EmployeeController::class, 'workDestroy'])->name('employees-nonactive.work.destroy');

    Route::get('/employees/employees-nonactive/asset/{id}/create', [EmployeeController::class, 'assetCreate'])->name('employees-nonactive.asset.create');
    Route::post('/employees/employees-nonactive/asset/{id}', [EmployeeController::class, 'assetStore'])->name('employees-nonactive.asset.store');
    Route::get('/employees/employees-nonactive/asset/{id}/edit', [EmployeeController::class, 'assetEdit'])->name('employees-nonactive.asset.edit');
    Route::patch('/employees/employees-nonactive/asset/{id}', [EmployeeController::class, 'assetUpdate'])->name('employees-nonactive.asset.update');
    Route::delete('/employees/employees-nonactive/asset/{id}', [EmployeeController::class, 'assetDestroy'])->name('employees-nonactive.asset.destroy');

    Route::get('/employees/employees-nonactive/file/{id}/create', [EmployeeController::class, 'fileCreate'])->name('employees-nonactive.file.create');
    Route::post('/employees/employees-nonactive/file/{id}', [EmployeeController::class, 'fileStore'])->name('employees-nonactive.file.store');
    Route::get('/employees/employees-nonactive/file/{id}/edit', [EmployeeController::class, 'fileEdit'])->name('employees-nonactive.file.edit');
    Route::patch('/employees/employees-nonactive/file/{id}', [EmployeeController::class, 'fileUpdate'])->name('employees-nonactive.file.update');
    Route::delete('/employees/employees-nonactive/file/{id}', [EmployeeController::class, 'fileDestroy'])->name('employees-nonactive.file.destroy');

    Route::get('/employees/contacts/data', [EmployeeContactController::class, 'data'])->name('contacts.data');
    Route::get('/employees/contacts/export', [EmployeeContactController::class, 'export'])->name('contacts.export');
    Route::get('/employees/contacts/import', [EmployeeContactController::class, 'import'])->name('contacts.import');
    Route::patch('/employees/contacts/process-import', [EmployeeContactController::class, 'processImport'])->name('contacts.process-import');
    Route::resource('/employees/contacts', EmployeeContactController::class);

    Route::get('/employees/families/data', [EmployeeFamilyController::class, 'data'])->name('families.data');
    Route::get('/employees/families/export', [EmployeeFamilyController::class, 'export'])->name('families.export');
    Route::get('/employees/families/import', [EmployeeFamilyController::class, 'import'])->name('families.import');
    Route::patch('/employees/families/process-import', [EmployeeFamilyController::class, 'processImport'])->name('families.process-import');
    Route::resource('/employees/families', EmployeeFamilyController::class);

    Route::get('/employees/educations/data', [EmployeeEducationController::class, 'data'])->name('educations.data');
    Route::get('/employees/educations/export', [EmployeeEducationController::class, 'export'])->name('educations.export');
    Route::get('/employees/educations/import', [EmployeeEducationController::class, 'import'])->name('educations.import');
    Route::patch('/employees/educations/process-import', [EmployeeEducationController::class, 'processImport'])->name('educations.process-import');
    Route::resource('/employees/educations', EmployeeEducationController::class);

    Route::get('/employees/trainings/data', [EmployeeTrainingController::class, 'data'])->name('trainings.data');
    Route::get('/employees/trainings/export', [EmployeeTrainingController::class, 'export'])->name('trainings.export');
    Route::resource('/employees/trainings', EmployeeTrainingController::class);

    Route::get('/employees/works/data', [EmployeeWorkController::class, 'data'])->name('works.data');
    Route::get('/employees/works/export', [EmployeeWorkController::class, 'export'])->name('works.export');
    Route::resource('/employees/works', EmployeeWorkController::class);

    Route::get('/employees/assets/data', [EmployeeAssetController::class, 'data'])->name('assets.data');
    Route::get('/employees/assets/export', [EmployeeAssetController::class, 'export'])->name('assets.export');
    Route::resource('/employees/assets', EmployeeAssetController::class);

    Route::get('/employees/files/data', [EmployeeFileController::class, 'data'])->name('files.data');
    Route::get('/employees/files/export', [EmployeeFileController::class, 'export'])->name('files.export');
    Route::resource('/employees/files', EmployeeFileController::class);

    Route::get('/employees/terminations/data', [EmployeeTerminationController::class, 'data'])->name('terminations.data');
    Route::get('/employees/terminations/export', [EmployeeTerminationController::class, 'export'])->name('terminations.export');
    Route::get('/employees/terminations/employee', [EmployeeTerminationController::class, 'employee'])->name('terminations.employee');
    Route::get('/employees/terminations/{id}/approve', [EmployeeTerminationController::class, 'approve'])->name('terminations.approve');
    Route::patch('/employees/terminations/{id}/updateApprove', [EmployeeTerminationController::class, 'updateApprove'])->name('terminations.updateApprove');
    Route::resource('/employees/terminations', EmployeeTerminationController::class);

    Route::get('/employees/rehireds/data', [EmployeeRehiredController::class, 'data'])->name('rehireds.data');
    Route::get('/employees/rehireds/export', [EmployeeRehiredController::class, 'export'])->name('rehireds.export');
    Route::get('/employees/rehireds/employee', [EmployeeRehiredController::class, 'employee'])->name('rehireds.employee');
    Route::get('/employees/rehireds/{id}/approve', [EmployeeRehiredController::class, 'approve'])->name('rehireds.approve');
    Route::patch('/employees/rehireds/{id}/updateApprove', [EmployeeRehiredController::class, 'updateApprove'])->name('rehireds.updateApprove');
    Route::resource('/employees/rehireds', EmployeeRehiredController::class);

    Route::get('/employees/position-histories/export', [EmployeePositionHistoryController::class, 'export'])->name('position-histories.export');
    Route::get('/employees/position-histories/import', [EmployeePositionHistoryController::class, 'import'])->name('position-histories.import');
    Route::patch('/employees/position-histories/process-import', [EmployeePositionHistoryController::class, 'processImport'])->name('position-histories.process-import');
    Route::resource('/employees/position-histories', EmployeePositionHistoryController::class);
    Route::get('/employees/position-histories/subMasters/{id}', [EmployeePositionHistoryController::class, 'subMasters'])->name('position-histories.subMasters');

    Route::resource('/employees/setting-unit-structures', EmployeeUnitStructureController::class);

    Route::resource('/employees/signatures', EmployeeSignatureSettingController::class);
});


