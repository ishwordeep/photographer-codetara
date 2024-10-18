<?php

use App\Http\Middleware\CheckSuperadmin;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Http\Request;



//Auth API Routes
Route::middleware('throttle:rate-limiter')->group(function () {

    // Authentications
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/create-admin-staff', [AuthController::class, 'register'])->middleware(['auth:sanctum',CheckSuperadmin::class]);
    
    Route::post('/login', [AuthController::class, 'login']);
});



// Verification of Email
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed','throttle:rate-limiter'])
    ->name('verification.verify');



//Resend Email Verification Link
Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
    ->middleware(['auth:sanctum','throttle:rate-limiter']);



//verified/not-verified email message route
Route::get('/email-verify-notification', function () {
    return response()->json(['message' => 'Email address is not verified. Please verify your email address.'], 403);
})->middleware('auth:sanctum')->name('verification.notice');

Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');