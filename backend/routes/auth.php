<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::post('/signup', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('signup');

Route::post('/signin', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('signin');

Route::post('/signout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum')
    ->name('signout');
