<?php

use App\Http\Controllers\Public\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)->group(function (){
    Route::get('/register_page','create')->name('register_page');
    Route::post('/register','store')->name('register');
    Route::get('/','index')->name('list');
});
