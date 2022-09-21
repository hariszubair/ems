<?php

use App\Http\Controllers\Backend\AttendanceController;
use App\Http\Controllers\Backend\LeaveController;
use App\Http\Controllers\Backend\ChangePasswordController;
use App\Http\Controllers\Backend\ReportController;
use App\Http\Controllers\Backend\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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


Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('isVerified');
Route::get('/verify', [App\Http\Controllers\HomeController::class, 'verify'])->name('verify');

Auth::routes();

Route::resource('users', UserController::class)->middleware(['admin']);


//Leave
Route::get('/leave/index', [LeaveController::class, 'index'])->name('leaves.index')->middleware(['adminManager']);
Route::get('/leave/apply', [LeaveController::class, 'apply'])->name('leaves.apply')->middleware(['applyLeave']);
Route::post('/leave/store', [LeaveController::class, 'store'])->name('leaves.store')->middleware(['applyLeave']);;
Route::get('/leaves/{leave}', [LeaveController::class, 'edit'])->name('leaves.edit')->middleware(['applyLeave']);;
Route::patch('/leaves/{leave}', [LeaveController::class, 'update'])->name('leaves.update')->middleware(['applyLeave']);;
Route::delete('/leaves/{leave}', [LeaveController::class, 'destroy'])->name('leaves.destroy')->middleware(['applyLeave']);;
Route::get('/leaves/{leave}/action', [LeaveController::class, 'action'])->name('leaves.action')->middleware(['adminManager']);
Route::Patch('/leave/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve')->middleware(['adminManager']);
Route::get('/leave/own', [LeaveController::class, 'own'])->name('leaves.own')->middleware(['applyLeave']);

Route::post('/leave/carry_forward', [LeaveController::class, 'carry_forward'])->name('leaves.carry_forward')->middleware(['admin']);;


//Attendance 

Route::get('/attendance/mark', [AttendanceController::class, 'mark'])->name('attendance.mark')->middleware(['adminManager']);
Route::post('/attendance/store/{id}', [AttendanceController::class, 'store'])->name('attendance.store')->middleware(['adminManager']);
Route::get('/attendance/mark_old', [AttendanceController::class, 'mark_old'])->name('attendance.mark_old')->middleware(['admin']);


//reports
Route::get('/reports', [ReportController::class, 'create'])->name('reports.create')->middleware(['adminManager']);
Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate')->middleware(['adminManager']);




Route::post('users/{user}/change-password', [ChangePasswordController::class, 'change_password'])->name('users.change.password');
Route::post('users/{user}/change-leaves', [UserController::class, 'change_leaves'])->name('users.change.leaves');
Route::get('users/{user}/verify', [UserController::class, 'verify_user'])->name('users.verify');



Route::get('/clear-cache', function () {
  $exitCode = Artisan::call('optimize:clear');
  // return what you want
});
Route::get('/migration', function () {
  $exitCode = Artisan::call('migrate');
  // return what you want
});
