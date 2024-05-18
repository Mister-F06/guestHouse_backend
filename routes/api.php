<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\GuestHouseController;
use App\Http\Controllers\Api\UserController;
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

    Route::prefix('users')->controller(UserController::class)->group(function() {
        Route::get('me' , 'me');
    });

    Route::prefix('guest_house')->controller(GuestHouseController::class)->group(function() {

        Route::middleware('role:admin')->group(function(){
            Route::get('list' , 'index');
        });

        Route::middleware('role:manager')->group(function() {
            Route::get('' , 'indexManager');
            Route::get('find/{guestHouse}' , 'show');
            Route::post('' , 'store');
            Route::post('update/{guestHouse}' , 'update');
            Route::delete('delete/{guestHouse}' , 'destroy');
        });
    });
});


// visitor