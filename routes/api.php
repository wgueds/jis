<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\ReleaseController;

Route::prefix('user')->controller(AuthController::class)->group(function() {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum')->group(function ()  {
    Route::resource('banks', BankController::class);
    Route::resource('releases', ReleaseController::class);
});