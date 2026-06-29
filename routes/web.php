<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\BusinessProfileController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\FacebookController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ContactMessageController;

use App\Models\BusinessProfile;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    // Fetch featured companies with category and city relationships eager loaded
    $featuredCompanies = BusinessProfile::with(['category', 'city'])
        ->where('status', 'approved')
        ->inRandomOrder()
        ->limit(6)
        ->get();

    return view('welcome', compact('featuredCompanies'));
})->name('home');

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar', 'es', 'de', 'zh', 'tr'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::get('auth/facebook', [FacebookController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('auth/facebook/callback', [FacebookController::class, 'handleFacebookCallback']);

Route::middleware(['auth', 'verified'])->group(function () {

    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart-data', [Admin\DashboardController::class, 'chartData'])->name('dashboard.chart-data');
        Route::view('/coming-soon', 'admin.coming-soon')->name('coming-soon');
        Route::get('/search', [Admin\GlobalSearchController::class, 'index'])->name('search');
        Route::resource('categories', Admin\CategoryController::class);
        Route::patch('/categories/{category}/status', [Admin\CategoryController::class, 'updateStatus'])->name('categories.update-status');
        
        Route::get('/locations', [Admin\LocationController::class, 'index'])->name('locations.index');
        
        Route::resource('countries', Admin\CountryController::class);
        Route::patch('/countries/{country}/status', [Admin\CountryController::class, 'updateStatus'])->name('countries.update-status');
        
        Route::resource('cities', Admin\CityController::class);
        Route::patch('/cities/{city}/status', [Admin\CityController::class, 'updateStatus'])->name('cities.update-status');
        
        Route::patch('/users/{user}/role', [Admin\UserController::class, 'updateRole'])->name('users.update-role');
        Route::resource('users', Admin\UserController::class)->only(['index', 'destroy']);
        
        Route::patch('/leads/{lead}/status', [Admin\LeadController::class, 'updateStatus'])->name('leads.update-status');
        Route::resource('leads', Admin\LeadController::class)->only(['index', 'show', 'destroy']);
        
        Route::get('/backups', [Admin\BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups', [Admin\BackupController::class, 'create'])->name('backups.create');
        Route::get('/backups/download', [Admin\BackupController::class, 'download'])->name('backups.download');
        Route::delete('/backups', [Admin\BackupController::class, 'destroy'])->name('backups.destroy');
        Route::post('/backups/settings', [Admin\BackupController::class, 'updateSettings'])->name('backups.settings');
        
        Route::get('/businesses/check-slug', [Admin\BusinessController::class, 'checkSlug'])->name('businesses.check-slug');
        Route::get('/businesses/search-users', [Admin\BusinessController::class, 'searchUsers'])->name('businesses.search-users');
        Route::post('/businesses/bulk-status', [Admin\BusinessController::class, 'bulkStatus'])->name('businesses.bulk-status');
        Route::resource('businesses', Admin\BusinessController::class);
        Route::get('/countries/{country}/cities', [Admin\BusinessController::class, 'getCitiesByCountry'])->name('countries.cities');
        Route::patch('/businesses/{business}/status', [Admin\BusinessController::class, 'updateStatus'])->name('businesses.update-status');
        Route::post('/businesses/{business}/claim', [Admin\BusinessController::class, 'claim'])->name('businesses.claim');
        Route::get('/profile', [Admin\ProfileController::class, 'index'])->name('profile');
        Route::patch('/profile', [Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile/password', [Admin\ProfileController::class, 'updatePassword'])->name('profile.password');
        
        Route::get('/settings', [Admin\SettingsController::class, 'index'])->name('settings');
        Route::patch('/settings', [Admin\SettingsController::class, 'update'])->name('settings.update');
    });

    Route::middleware(['user'])->prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/views-chart', [DashboardController::class, 'viewsChart'])->name('dashboard.views-chart');

        Route::prefix('business')->group(function () {
            Route::get('/', [BusinessProfileController::class, 'index'])->name('business.index');
            Route::get('/create', [BusinessProfileController::class, 'create'])->name('business.create');
            Route::get('/check-slug', [BusinessProfileController::class, 'checkSlug'])->name('business.check-slug');
            Route::post('/store', [BusinessProfileController::class, 'store'])->name('business.store');
            Route::get('/edit', [BusinessProfileController::class, 'edit'])->name('business.edit');
            Route::put('/update', [BusinessProfileController::class, 'update'])->name('business.update');

            Route::post('/sections/sync', [BusinessProfileController::class, 'syncSections'])->name('business.sections.sync');

            Route::post('/media', [BusinessProfileController::class, 'uploadMedia'])->name('business.media.upload');
            Route::post('/media/order', [BusinessProfileController::class, 'updateMediaOrder'])->name('business.media.order');
            Route::post('/media/{id}/caption', [BusinessProfileController::class, 'updateMediaCaption'])->name('business.media.caption');
            Route::delete('/media/{id}', [BusinessProfileController::class, 'destroyMedia'])->name('business.media.destroy');
        });

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

require __DIR__ . '/auth.php';

Route::get('/directory', [DirectoryController::class, 'index'])->name('directory.index');
Route::get('/directory/search', [DirectoryController::class, 'liveSearch'])->name('directory.search');
Route::get('/directory/{slug}', [BusinessProfileController::class, 'show'])->name('directory.business.view');

Route::view('/about', 'about')->name('about');
Route::get('/contact', function () {
    return view('contact');
})->name('contact');
Route::post('/contact', [ContactMessageController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

// Legal Pages
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/terms', 'pages.terms')->name('terms');
Route::view('/cookies', 'pages.cookies')->name('cookies');

// Chatbot Route
Route::post('/chatbot/ask', [ChatbotController::class, 'ask'])
    ->middleware('throttle:30,1')
    ->name('chatbot.ask');

Route::get('/{slug}', [BusinessProfileController::class, 'show'])->name('business.view');