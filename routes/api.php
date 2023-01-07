<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

Route::prefix('auth')->as('auth')->group(function(){
    Route::post('login',[AuthController::class,'login'])->name('login');
    Route::post('register',[AuthController::class,'register'])->name('register');
    Route::post('login_with_token',[AuthController::class,'loginWithToken'])->middleware('auth:sanctum')->name('loginWithToken');
    Route::get('logout',[AuthController::class,'logout'])->middleware('auth:sanctum')->name('logout');
});

Route::middleware('auth:sanctum')->group(function (){
    Route::apiResource('chat', ChatController::class)->only(['index','store','show']);
    Route::apiResource('chat_message', ChatMessageController::class)->only(['index','store']);
    Route::apiResource('user', UserController::class)->only(['index']);
});
