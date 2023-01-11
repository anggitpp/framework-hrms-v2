<?php

use App\Http\Controllers\Attendance\AttendanceCorrectionController;
use App\Http\Controllers\Attendance\AttendanceDailyController;
use App\Http\Controllers\Attendance\AttendanceDurationRecapController;
use App\Http\Controllers\Attendance\AttendanceHolidayController;
use App\Http\Controllers\Attendance\AttendanceLeaveController;
use App\Http\Controllers\Attendance\AttendanceLeaveMasterController;
use App\Http\Controllers\Attendance\AttendanceLocationSettingController;
use App\Http\Controllers\Attendance\AttendanceMachineController;
use App\Http\Controllers\Attendance\AttendanceMonthlyController;
use App\Http\Controllers\Attendance\AttendanceOvertimeController;
use App\Http\Controllers\Attendance\AttendancePermissionController;
use App\Http\Controllers\Attendance\AttendanceRecapController;
use App\Http\Controllers\Attendance\AttendanceShiftController;
use App\Http\Controllers\Attendance\AttendanceTimesheetController;
use App\Http\Controllers\Attendance\AttendanceTimesheetRecapController;
use App\Http\Controllers\Attendance\AttendanceWorkScheduleController;
use Illuminate\Support\Facades\Route;

Route::name('attendances.')->group(function () {
    Route::resource('/attendances/location-settings', AttendanceLocationSettingController::class);

    Route::get('/attendances/dailies/export', [AttendanceDailyController::class, 'export'])->name('dailies.export');
    Route::get('/attendances/dailies/data', [AttendanceDailyController::class, 'data'])->name('dailies.data');
    Route::resource('/attendances/dailies', AttendanceDailyController::class);

    Route::get('/attendances/monthlies', [AttendanceMonthlyController::class, 'index'])->name('monthlies.index');
    Route::post('/attendances/monthlies/data/{month}/{year}', [AttendanceMonthlyController::class, 'data'])->name('monthlies.data');
    Route::get('/attendances/monthlies/export', [AttendanceMonthlyController::class, 'export'])->name('monthlies.export');

    Route::get('/attendances/duration-recap', [AttendanceDurationRecapController::class, 'index'])->name('duration-recap.index');
    Route::post('/attendances/duration-recap/data/{month}/{year}', [AttendanceDurationRecapController::class, 'data'])->name('duration-recap.data');
    Route::get('/attendances/duration-recap/pdf', [AttendanceDurationRecapController::class, 'pdf'])->name('duration-recap.pdf');
    Route::get('/attendances/duration-recap/export', [AttendanceDurationRecapController::class, 'export'])->name('duration-recap.export');

    Route::get('/attendances/attendance-recap', [AttendanceRecapController::class, 'index'])->name('attendance-recap.index');
    Route::get('/attendances/attendance-recap/data/{month}/{year}', [AttendanceRecapController::class, 'data'])->name('attendance-recap.data');
    Route::get('/attendances/attendance-recap/export', [AttendanceRecapController::class, 'export'])->name('attendance-recap.export');
    Route::get('/attendances/attendance-recap/pdf', [AttendanceRecapController::class, 'pdf'])->name('attendance-recap.pdf');

    Route::resource('/attendances/shifts', AttendanceShiftController::class);

    Route::resource('/attendances/holidays', AttendanceHolidayController::class);

    Route::resource('/attendances/machines', AttendanceMachineController::class);

    Route::get('/attendances/leaves/data', [AttendanceLeaveController::class, 'data'])->name('leaves.data');
    Route::get('/attendances/leaves/employee', [AttendanceLeaveController::class, 'employee'])->name('leaves.employee');
    Route::get('/attendances/leaves/leave', [AttendanceLeaveController::class, 'leave'])->name('leaves.leave');
    Route::get('/attendances/leaves/totalLeave', [AttendanceLeaveController::class, 'totalLeave'])->name('leaves.totalLeave');
    Route::get('/attendances/leaves/{id}/approve', [AttendanceLeaveController::class, 'approve'])->name('leaves.approve');
    Route::patch('/attendances/leaves/{id}/updateApprove', [AttendanceLeaveController::class, 'updateApprove'])->name('leaves.updateApprove');
    Route::resource('/attendances/leaves', AttendanceLeaveController::class);

    Route::get('/attendances/permissions/data', [AttendancePermissionController::class, 'data'])->name('permissions.data');
    Route::get('/attendances/permissions/employee', [AttendancePermissionController::class, 'employee'])->name('permissions.employee');
    Route::get('/attendances/permissions/{id}/approve', [AttendancePermissionController::class, 'approve'])->name('permissions.approve');
    Route::patch('/attendances/permissions/{id}/updateApprove', [AttendancePermissionController::class, 'updateApprove'])->name('permissions.updateApprove');
    Route::resource('/attendances/permissions', AttendancePermissionController::class);

    Route::get('/attendances/overtimes/data', [AttendanceOvertimeController::class, 'data'])->name('overtimes.data');
    Route::get('/attendances/overtimes/employee', [AttendanceOvertimeController::class, 'employee'])->name('overtimes.employee');
    Route::get('/attendances/overtimes/{id}/approve', [AttendanceOvertimeController::class, 'approve'])->name('overtimes.approve');
    Route::patch('/attendances/overtimes/{id}/updateApprove', [AttendanceOvertimeController::class, 'updateApprove'])->name('overtimes.updateApprove');
    Route::resource('/attendances/overtimes', AttendanceOvertimeController::class);

    Route::get('/attendances/corrections/attendance', [AttendanceCorrectionController::class, 'attendance'])->name('corrections.attendance');
    Route::get('/attendances/corrections/data', [AttendanceCorrectionController::class, 'data'])->name('corrections.data');
    Route::get('/attendances/corrections/employee', [AttendanceCorrectionController::class, 'employee'])->name('corrections.employee');
    Route::get('/attendances/corrections/{id}/approve', [AttendanceCorrectionController::class, 'approve'])->name('corrections.approve');
    Route::patch('/attendances/corrections/{id}/updateApprove', [AttendanceCorrectionController::class, 'updateApprove'])->name('corrections.updateApprove');
    Route::resource('/attendances/corrections', AttendanceCorrectionController::class);

    Route::get('/attendances/leave-masters/data', [AttendanceLeaveMasterController::class, 'data'])->name('leave-masters.data');
    Route::resource('/attendances/leave-masters', AttendanceLeaveMasterController::class);

    Route::get('/attendances/work-schedule', [AttendanceWorkScheduleController::class, 'index'])->name('work-schedule.index');
    Route::get('/attendances/work-schedule/edit/{id}/{date}', [AttendanceWorkScheduleController::class, 'edit'])->name('work-schedule.edit');
    Route::patch('/attendances/work-schedule/update/{id}/{date}', [AttendanceWorkScheduleController::class, 'update'])->name('work-schedule.update');
    Route::get('/attendances/work-schedule/shift', [AttendanceWorkScheduleController::class, 'shift'])->name('work-schedule.shift');
    Route::get('/attendances/work-schedule/sync', [AttendanceWorkScheduleController::class, 'sync'])->name('work-schedule.sync');
    Route::post('/attendances/work-schedule/process-sync', [AttendanceWorkScheduleController::class, 'processSync'])->name('work-schedule.process-sync');

    Route::get('/attendances/timesheets/data', [AttendanceTimesheetController::class, 'data'])->name('timesheets.data');
    Route::get('/attendances/timesheets/employee', [AttendanceTimesheetController::class, 'employee'])->name('timesheets.employee');
    Route::resource('/attendances/timesheets', AttendanceTimesheetController::class);

    Route::get('/attendances/timesheet-recap', [AttendanceTimesheetRecapController::class, 'index'])->name('timesheet-recap.index');
    Route::get('/attendances/timesheet-recap/data/{month}/{year}', [AttendanceTimesheetRecapController::class, 'data'])->name('timesheet-recap.data');
    Route::get('/attendances/timesheet-recap/detail/{id}/{date}', [AttendanceTimesheetRecapController::class, 'detail'])->name('timesheet-recap.detail');
    Route::get('/attendances/timesheet-recap/export', [AttendanceTimesheetRecapController::class, 'export'])->name('timesheet-recap.export');
    Route::get('/attendances/timesheet-recap/pdf', [AttendanceTimesheetRecapController::class, 'pdf'])->name('timesheet-recap.pdf');
});


