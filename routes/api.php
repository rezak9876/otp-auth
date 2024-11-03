<?php

use Illuminate\Support\Facades\Route;
use RezaK\OtpAuth\Http\Controllers\OtpController;

Route::prefix('api/auth/otp')->group(function () {
    Route::post('send', [OtpController::class, 'sendOtp']);
    Route::post('verify', [OtpController::class, 'verifyOtp']);
});