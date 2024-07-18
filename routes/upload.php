<?php
use Illuminate\Support\Facades\Route;

Route::post('upload/media', [\App\Http\Controllers\UploadController::class, 'media'])->name('upload.media');
Route::post('upload/image', [\App\Http\Controllers\UploadController::class, 'image'])->name('upload.image');
Route::post('upload/crop', [\App\Http\Controllers\UploadController::class, 'crop'])->name('upload.crop');
Route::post('upload/multiple', [\App\Http\Controllers\UploadController::class, 'multiple'])->name('upload.multiple');
