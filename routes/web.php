<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\BusinessProfileController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\FacebookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::get('auth/facebook', [FacebookController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('auth/facebook/callback', [FacebookController::class, 'handleFacebookCallback']);

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('business')->group(function () {
        Route::get('/', [BusinessProfileController::class, 'index'])->name('business.index');
        Route::get('/create', [BusinessProfileController::class, 'create'])->name('business.create');
        Route::post('/store', [BusinessProfileController::class, 'store'])->name('business.store');
        Route::get('/{id}/edit', [BusinessProfileController::class, 'edit'])->name('business.edit');
        Route::put('/{id}/update', [BusinessProfileController::class, 'update'])->name('business.update');
        Route::post('/{id}/media', [BusinessProfileController::class, 'uploadMedia'])->name('business.media.upload');
        Route::delete('/media/{id}', [BusinessProfileController::class, 'destroyMedia'])->name('business.media.destroy');
    });

    Route::prefix('sections')->group(function () {
        Route::post('/', [BusinessProfileController::class, 'storeSection'])->name('sections.store');
        Route::put('/{id}', [BusinessProfileController::class, 'updateSection'])->name('sections.update');
        Route::delete('/{id}', [BusinessProfileController::class, 'destroySection'])->name('sections.destroy');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::get('/{slug}', [BusinessProfileController::class, 'showPublicProfile'])->name('profile.show');
