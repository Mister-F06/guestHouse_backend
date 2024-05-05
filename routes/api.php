<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function() {

    Route::post('login' , [LoginController::class , 'authenticate']);

    Route::post('register' , [RegisterController::class , 'register']);

    Route::post('verify/email' , [RegisterController::class , 'verifyEmail'])->name('email.verify');

    Route::put('resent/verify/link' , [RegisterController::class , 'resentLink']);

    Route::post('/reset-password' , [LoginController::class , 'resetPassword']);

    Route::post('/reset-password/verify/signature' , [LoginController::class , 'verifySignature '])->name('reset.password.verify');

    Route::post('/reset-password/resent/link' , [LoginController::class , 'resentPasswordLink']);

    Route::put('/reset-password' , [LoginController::class , 'changePassword']);

});


Route::middleware('auth:sanctum')->group(function(){

});