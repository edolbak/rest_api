<?php

use App\Http\Controllers\API\PositionController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('v1/users', UserController::class);

Route::get('v1/positions', [PositionController::class, 'index']);
Route::get('v1/token', [UserController::class, 'token'])->name('token');
Route::get('v1/validate_token', [UserController::class, 'validate_token'])->name('validate_token');

//Route::get('v1/users', [UserController::class, 'index']);
//Route::prefix('admin')->controller(UserController::class)->group(function (){
//    Route::get('/test','index');
////    Route::get('/test1','test1');
//});
//
//Route::controller(UserController::class)->group(function (){
//    Route::get('/register','index')->name('register');
//    Route::get('/login','index')->name('login');
////    Route::get('/test1','test1');
//});
