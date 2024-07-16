<?php

use Illuminate\Support\Facades\Route;
//, 'middleware' => 'simple.acl'
Route::group(['prefix' => 'admin'], function () {
    Route::get('/', function(){
        return view('dashboard');
    })->name('admin');
});