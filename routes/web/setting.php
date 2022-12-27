<?php

use App\Http\Controllers\Setting\AppMasterDataController;
use App\Http\Controllers\Setting\AppMenuController;
use App\Http\Controllers\Setting\AppModulController;
use App\Http\Controllers\Setting\AppParameterController;
use App\Http\Controllers\Setting\AppSubModulController;
use App\Http\Controllers\Setting\UserAccessController;
use App\Http\Controllers\Setting\UserController;
use App\Http\Controllers\Setting\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::name('settings.')->group(function () {
    Route::get('/settings/users/reset-password/{id}', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::patch('/settings/users/update-password/{id}', [UserController::class, 'updatePassword'])->name('users.update-password');
    Route::resource('/settings/users', UserController::class);

    Route::resource('/settings/user-roles', UserRoleController::class);

    Route::resource('/settings/user-access', UserAccessController::class);

    Route::resource('/settings/app-moduls', AppModulController::class);

    Route::resource('/settings/app-sub-moduls', AppSubModulController::class);

    Route::resource('/settings/app-menus', AppMenuController::class);
    Route::get('/settings/app-menus/sub-moduls/{id}', [AppMenuController::class, 'subModuls'])->name('app-menus.sub-moduls');
    Route::get('/settings/app-menus/menus/{id}', [AppMenuController::class, 'menus'])->name('app-menus.menus');
    Route::get('/settings/app-menus/{id}/create-child', [AppMenuController::class, 'createChild'])->name('app-menus.create-child');

    Route::get('/settings/app-master-datas/{id}/create-master', [AppMasterDataController::class, 'createMaster'])->name('app-master-datas.create-master');
    Route::post('/settings/app-master-datas/{id}/store-master', [AppMasterDataController::class, 'storeMaster'])->name('app-master-datas.store-master');
    Route::get('/settings/app-master-datas/{id}/edit-master', [AppMasterDataController::class, 'editMaster'])->name('app-master-datas.edit-master');
    Route::patch('/settings/app-master-datas/{id}/update-master', [AppMasterDataController::class, 'updateMaster'])->name('app-master-datas.update-master');
    Route::delete('/settings/app-master-datas/{id}/destroy-master', [AppMasterDataController::class, 'destroyMaster'])->name('app-master-datas.destroy-master');
    Route::resource('/settings/app-master-datas', AppMasterDataController::class);

    Route::get('/settings/app-parameters/data', [AppParameterController::class, 'data'])->name('app-parameters.data');
    Route::resource('/settings/app-parameters', AppParameterController::class);
});
