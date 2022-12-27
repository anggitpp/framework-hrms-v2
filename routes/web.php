<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\Attendance\AttendanceCorrectionController;
use App\Http\Controllers\Attendance\AttendanceDailyController;
use App\Http\Controllers\Attendance\AttendanceHolidayController;
use App\Http\Controllers\Attendance\AttendanceLeaveController;
use App\Http\Controllers\Attendance\AttendanceLeaveMasterController;
use App\Http\Controllers\Attendance\AttendanceLocationSettingController;
use App\Http\Controllers\Attendance\AttendanceMonthlyController;
use App\Http\Controllers\Attendance\AttendanceDurationRecapController;
use App\Http\Controllers\Attendance\AttendanceOvertimeController;
use App\Http\Controllers\Attendance\AttendancePermissionController;
use App\Http\Controllers\Attendance\AttendanceRecapController;
use App\Http\Controllers\Attendance\AttendanceShiftController;
use App\Http\Controllers\Attendance\AttendanceWorkScheduleController;
use App\Http\Controllers\Attendance\AttendanceMachineController;
use App\Http\Controllers\ESS\ESSAttendanceRecapController;
use App\Http\Controllers\ESS\ESSCorrectionController;
use App\Http\Controllers\ESS\ESSOvertimeController;
use App\Http\Controllers\ESS\ESSPermissionController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\EmployeePositionHistoryController;
use App\Http\Controllers\Employee\EmployeeUnitStructureController;
use App\Http\Controllers\ESS\ESSLeaveController;
use App\Http\Controllers\ESS\EssTimesheetController;
use App\Http\Controllers\MigrationController;
use App\Http\Controllers\Setting\AppMasterDataController;
use App\Http\Controllers\Setting\AppMenuController;
use App\Http\Controllers\Setting\AppModulController;
use App\Http\Controllers\Setting\AppParameterController;
use App\Http\Controllers\Setting\AppSubModulController;
use App\Http\Controllers\Setting\UserAccessController;
use App\Http\Controllers\Setting\UserController;
use App\Http\Controllers\Setting\UserRoleController;
use App\Models\Attendance\AttendanceLocationSetting;
use App\Models\Setting\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if(Auth::check()){
        $firstMenu = DB::table('app_menus as t1')
            ->select('t4.name')
            ->join('app_moduls as t2', 't1.app_modul_id', 't2.id')
            ->join('app_sub_moduls as t3', 't1.app_sub_modul_id', 't3.id')
            ->join('app_permissions as t4', 't1.id', 't4.type_id')
            ->join('app_role_has_permissions as t5', 't4.id', 't5.permission_id')
            ->where('t1.status', 't')
            ->where('t2.status', 't')
            ->where('t4.method', 'view')
            ->where('t4.type', 'menu')
            ->where('t5.role_id', Auth::user()->role_id)
            ->orderBy('t2.order')
            ->orderBy('t3.order')
            ->orderBy('t1.order')
            ->first();
        $route = str_replace("view ","", str_replace("/", ".", $firstMenu->name).".index");

        return redirect()->route($route);
    }

    return view('auth.login');
});


Auth::routes();
//SETTING ROUTE
require_once __DIR__ . '/web/setting.php';

//EMPLOYEE ROUTE
require_once __DIR__ . '/web/employee.php';

//ATTENDANCE ROUTE
require_once __DIR__ . '/web/attendance.php';

//ESS ROUTE
require_once __DIR__ . '/web/ess.php';

//PAYROLL ROUTE
require_once __DIR__ . '/web/payroll.php';

Route::name('dashboard.')->group(function () {
    Route::resource('/dashboard/employess', UserController::class);
});

//APP ROUTES
Route::get('/app/edit-profile/{currentRoute}', [AppController::class, 'editProfile'])->name('app.edit-profile');
Route::patch('/app/update-profile/{currentRoute}', [AppController::class, 'updateProfile'])->name('app.update-profile');
Route::get('/app/edit-password/{currentRoute}', [AppController::class, 'editPassword'])->name('app.edit-password');
Route::patch('/app/update-password/{currentRoute}', [AppController::class, 'updatePassword'])->name('app.update-password');

Route::get('/app/update-menu', [AppController::class, 'updateMenu'])->name('app.update-menu');

Route::get('/migration/update-pin', [MigrationController::class, 'updatePin'])->name('migration.update-pin');

Route::get('/clear', function() {

    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');

    Artisan::call('route:cache');
    Artisan::call('config:cache');
    Artisan::call('view:cache');

    return "Cleared!";

});
