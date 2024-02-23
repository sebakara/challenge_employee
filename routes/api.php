<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::middleware('auth:sanctum')->group(function() {
    Route::get('/me', [UserController::class, 'me']);
    Route::post('/attendance',[AttendanceController::class, 'recordAttendance']);
    Route::post('/attendance_report',[AttendanceController::class, 'attendanceReport']);
    Route::post('/update_employee',[UserController::class, 'update']);
    Route::post('/logout',[UserController::class, 'logout']);
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();

});
Route::post('/register',[UserController::class,'store']);
Route::post('/login',[UserController::class,'login'])->name('login');
Route::post('forget_password',[UserController::class,'forgotPassword']);
Route::post('reset_password',[UserController::class, 'resetPassword']);



