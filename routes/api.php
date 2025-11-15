<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

Route::prefix('auth')
    ->controller(AuthController::class)
    ->group(function () {

    Route::post('register', 'register');
    Route::post('login', 'login')->name('login');

    Route::middleware('auth:sanctum')
        ->group(function () {

        Route::post('logout', 'logout');
    });
});

Route::prefix('profile')
    ->controller(ProfileController::class)
    ->middleware('auth:sanctum')
    ->group(function () {

    Route::get('me', 'me');
});
