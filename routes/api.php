<?php

use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\PerformanceController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\SubmissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    //HOME
    Route::get('/home', [HomeController::class, 'getInitialData']);
    Route::get('/home/attendances', [HomeController::class, 'getAttendances']);

    //ATTENDANCE
    Route::get('/attendance', [AttendanceController::class, 'attendance']);
    Route::post('/attendance/save', [AttendanceController::class, 'save']);
    Route::post('/attendance/upload-image', [AttendanceController::class, 'uploadImage']);

    //SUBMISSION
    Route::get('/submission/category', [SubmissionController::class, 'getCategory']);
    Route::post('/submission/upload-image', [SubmissionController::class, 'uploadImage']);
    Route::post('/submission/store', [SubmissionController::class, 'store']);
    Route::get('/submission/edit/{id}', [SubmissionController::class, 'edit']);
    Route::post('/submission/update/{id}', [SubmissionController::class, 'update']);
    Route::delete('/submission/delete/{id}', [SubmissionController::class, 'delete']);

    //PERFORMANCE
    Route::get('/performance', [PerformanceController::class, 'performances']);
    Route::post('/performance/store', [PerformanceController::class, 'store']);
    Route::get('/performance/edit/{id}', [PerformanceController::class, 'edit']);
    Route::post('/performance/update/{id}', [PerformanceController::class, 'update']);
    Route::delete('/performance/delete/{id}', [PerformanceController::class, 'delete']);
    Route::get('/performance/empty-list', [PerformanceController::class, 'empties']);

    //PROFILE
    Route::post('/profile/upload-image', [ProfileController::class, 'uploadImage']);
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword']);
});
