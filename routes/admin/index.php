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

    // media
    Route::get('media/index', [\App\Http\Controllers\Admin\MediaController::class, 'index'])->name('media.index');
    Route::get('media/crop', [\App\Http\Controllers\Admin\MediaController::class, 'crop'])->name('media.crop');
    Route::post('media/store', [\App\Http\Controllers\Admin\MediaController::class, 'store'])->name('media.upload');
});