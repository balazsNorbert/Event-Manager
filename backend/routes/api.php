<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::get('/password-reset/{token}', function ($token) {
    return response()->json(['message' => 'Frontend reset page placeholder']);
})->name('password.reset');

Route::middleware(['auth:api'])->group(function () {
    Route::get('profile', [AuthController::class, 'profile']);
    Route::get('/events', [EventController::class, 'index']);
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{id}', [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);
});