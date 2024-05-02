<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function() {

    Route::post('login' , [LoginController::class , 'authenticate']);

    Route::post('register' , [RegisterController::class , 'register']);

    Route::post('verify/email' , [RegisterController::class , 'verifyEmail'])->name('email.verify');

});