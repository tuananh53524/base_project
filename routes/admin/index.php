<?php

use App\Http\Controllers\Admin\DefaultController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
//, 'middleware' => 'simple.acl'
Route::group(['prefix' => 'admin','middleware' => 'simple.acl'], function () {
    Route::get('/', [DefaultController::class,'index'])->name('admin');
    Route::post('download-log', [DefaultController::class, 'download'])->name('download.log');
    Route::post('exec-command', [SettingController::class, 'execCommand']);

    // users
    Route::resource('users', UserController::class);
});