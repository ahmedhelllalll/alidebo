<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/dashboard/business/create', [DashboardController::class, 'create'])->name('business.create');
    Route::post('/dashboard/business/store', [DashboardController::class, 'store'])->name('business.store');
    Route::get('/dashboard/business/{id}/edit', [DashboardController::class, 'edit'])->name('business.edit');
    Route::put('/dashboard/business/{id}/update', [DashboardController::class, 'update'])->name('business.update');

    Route::post('/dashboard/business/{id}/media', [DashboardController::class, 'uploadMedia'])->name('business.media.upload');
    Route::delete('/dashboard/media/{id}', [DashboardController::class, 'destroyMedia'])->name('business.media.destroy');

    Route::post('/dashboard/sections', [DashboardController::class, 'storeSection'])->name('sections.store');
    Route::put('/dashboard/sections/{id}', [DashboardController::class, 'updateSection'])->name('sections.update');
    Route::delete('/dashboard/sections/{id}', [DashboardController::class, 'destroySection'])->name('sections.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';

Route::get('/{slug}', [DashboardController::class, 'showPublicProfile'])->name('profile.show');