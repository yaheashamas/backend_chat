<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->as('auth')->group(function(){
    Route::post('login',[AuthController::class,'login'])->name('login');
    Route::post('register',[AuthController::class,'register'])->name('register');
    Route::post('login_with_token',[AuthController::class,'loginWithToken'])->middleware('auth:sanctum')->name('loginWithToken');
    Route::get('logout',[AuthController::class,'logout'])->middleware('auth:sanctum')->name('logout');
});
