<?php

use App\Http\Controllers\ESS\ESSAssetController;
use App\Http\Controllers\ESS\ESSAttendanceRecapController;
use App\Http\Controllers\ESS\ESSBankController;
use App\Http\Controllers\ESS\ESSContactController;
use App\Http\Controllers\ESS\ESSCorrectionController;
use App\Http\Controllers\ESS\ESSDashboardController;
use App\Http\Controllers\ESS\ESSEducationController;
use App\Http\Controllers\ESS\ESSFamilyController;
use App\Http\Controllers\ESS\ESSFileController;
use App\Http\Controllers\ESS\ESSLeaveController;
use App\Http\Controllers\ESS\ESSOvertimeController;
use App\Http\Controllers\ESS\ESSPermissionController;
use App\Http\Controllers\ESS\ESSPositionController;
use App\Http\Controllers\ESS\ESSProfileController;
use App\Http\Controllers\ESS\EssTimesheetController;
use App\Http\Controllers\ESS\ESSTrainingController;
use App\Http\Controllers\ESS\ESSWorkController;
use Illuminate\Support\Facades\Route;

Route::name('ess.')->group(function () {
    Route::get('/ess/timesheets/data', [EssTimesheetController::class, 'data'])->name('timesheets.data');
    Route::resource('/ess/timesheets', EssTimesheetController::class);

    Route::get('/ess/leaves/data', [ESSLeaveController::class, 'data'])->name('leaves.data');
    Route::get('/ess/leaves/leave', [ESSLeaveController::class, 'leave'])->name('leaves.leave');
    Route::get('/ess/leaves/totalLeave', [ESSLeaveController::class, 'totalLeave'])->name('leaves.totalLeave');
    Route::resource('/ess/leaves', ESSLeaveController::class);

    Route::get('/ess/permissions/data', [ESSPermissionController::class, 'data'])->name('permissions.data');
    Route::resource('/ess/permissions', ESSPermissionController::class);

    Route::get('/ess/overtimes/data', [ESSOvertimeController::class, 'data'])->name('overtimes.data');
    Route::resource('/ess/overtimes', ESSOvertimeController::class);

    Route::get('/ess/corrections/attendance', [ESSCorrectionController::class, 'attendance'])->name('corrections.attendance');
    Route::get('/ess/corrections/data', [ESSCorrectionController::class, 'data'])->name('corrections.data');
    Route::resource('/ess/corrections', ESSCorrectionController::class);

    Route::get('/ess/attendance-recap', [ESSAttendanceRecapController::class, 'index'])->name('attendance-recap.index');

    Route::get('/ess/ess-dash', [ESSDashboardController::class, 'index'])->name('ess-dash.index');

    Route::get('/ess/profile', [ESSProfileController::class, 'index'])->name('profile.index');
    Route::get('/ess/profile/edit', [ESSProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/ess/profile', [ESSProfileController::class, 'update'])->name('profile.update');

    Route::get('/ess/contacts/data', [ESSContactController::class, 'data'])->name('contacts.data');
    Route::resource('/ess/contacts', ESSContactController::class);

    Route::get('/ess/families/data', [ESSFamilyController::class, 'data'])->name('families.data');
    Route::resource('/ess/families', ESSFamilyController::class);

    Route::get('/ess/educations/data', [ESSEducationController::class, 'data'])->name('educations.data');
    Route::resource('/ess/educations', ESSEducationController::class);

    Route::get('/ess/positions/data', [ESSPositionController::class, 'data'])->name('positions.data');
    Route::get('/ess/positions', [ESSPositionController::class, 'index'])->name('positions.index');

    Route::get('/ess/trainings/data', [ESSTrainingController::class, 'data'])->name('trainings.data');
    Route::resource('/ess/trainings', ESSTrainingController::class);

    Route::get('/ess/works/data', [ESSWorkController::class, 'data'])->name('works.data');
    Route::resource('/ess/works', ESSWorkController::class);

    Route::get('/ess/assets/data', [ESSAssetController::class, 'data'])->name('assets.data');
    Route::resource('/ess/assets', ESSAssetController::class);

    Route::get('/ess/files/data', [ESSFileController::class, 'data'])->name('files.data');
    Route::resource('/ess/files', ESSFileController::class);

    Route::get('/ess/banks/data', [ESSBankController::class, 'data'])->name('banks.data');
    Route::resource('/ess/banks', ESSBankController::class);
});


