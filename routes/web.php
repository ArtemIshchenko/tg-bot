<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramBotController;

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

Route::get('/home', function () {
    return view('welcome');
});
//Route::get('/bot', [TelegramBotController::class, 'index']);

Auth::routes();

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\HomeController::class, 'index'])->name('homeAdmin');
    Route::resource('channels', App\Http\Controllers\Admin\ChannelController::class);
    Route::get('/channels/{channel}/destroy', [App\Http\Controllers\Admin\ChannelController::class, 'destroy'])->name('channelDestroy');
    Route::resource('groups', App\Http\Controllers\Admin\GroupController::class);
    Route::get('/groups/{group}/destroy', [App\Http\Controllers\Admin\GroupController::class, 'destroy'])->name('groupDestroy');
    Route::resource('tasks', App\Http\Controllers\Admin\TaskController::class);
    Route::get('/tasks/{task}/destroy', [App\Http\Controllers\Admin\TaskController::class, 'destroy'])->name('taskDestroy');
    Route::resource('advisors', App\Http\Controllers\Admin\AdvisorController::class);
    Route::get('/channels/change-status/{id}/{status}', [App\Http\Controllers\Admin\ChannelController::class, 'changeStatus'])->name('channelChangeStatus');
    Route::get('/groups/change-status/{id}/{status}', [App\Http\Controllers\Admin\GroupController::class, 'changeStatus'])->name('groupChangeStatus');
    Route::get('/tasks/change-status/{id}/{status}', [App\Http\Controllers\Admin\TaskController::class, 'changeStatus'])->name('taskChangeStatus');
    Route::get('/users/{status?}', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users');
    Route::get('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('userShow');
    Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('userEdit');
    Route::put('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('userUpdate');
    Route::get('/requisitions/{status?}', [App\Http\Controllers\Admin\RequisitionController::class, 'index'])->name('requisitions');
    Route::get('/requisitions/change-status/{id}/{status}', [App\Http\Controllers\Admin\RequisitionController::class, 'changeStatus'])->name('requisitionChangeStatus');
    Route::match(['get', 'post', 'put'], '/settings',[App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings');
});
