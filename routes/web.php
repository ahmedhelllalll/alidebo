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
use App\Http\Controllers\PublicReviewController;

use App\Models\BusinessProfile;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

Route::get('/', function () {
    // Fetch featured companies with category and city relationships eager loaded (cached to avoid full table scans)
    $featuredCompanies = Cache::remember('featured_companies', now()->addMinutes(5), function () {
        return BusinessProfile::with(['category', 'city'])
            ->where('status', 'approved')
            ->inRandomOrder()
            ->limit(6)
            ->get();
    });

    return view('welcome', compact('featuredCompanies'));
})->name('home');

Route::get('/sitemap.xml', function (\App\Services\SitemapService $sitemapService) {
    if (ob_get_length()) ob_clean();
    return response($sitemapService->getCachedSitemap(), 200, [
        'Content-Type' => 'application/xml'
    ]);
})->name('sitemap.xml');

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
    Route::get('/api/countries/{country}/cities', function (\App\Models\Country $country) {
        return response()->json($country->cities);
    })->name('api.countries.cities');


    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart-data', [Admin\DashboardController::class, 'chartData'])->name('dashboard.chart-data');
        Route::view('/coming-soon', 'admin.coming-soon')->name('coming-soon');
        Route::get('/search', [Admin\GlobalSearchController::class, 'index'])->name('search');
        Route::resource('categories', Admin\CategoryController::class);
        Route::patch('/categories/{category}/status', [Admin\CategoryController::class, 'updateStatus'])->name('categories.update-status');
        
        Route::get('/pages/check-slug', [Admin\PageController::class, 'checkSlug'])->name('pages.check-slug');
        Route::resource('pages', Admin\PageController::class);
        
        Route::get('/dashboard/seo', [Admin\SeoController::class, 'dashboard'])->name('dashboard.seo');
        Route::post('/dashboard/seo/sitemap/regenerate', [Admin\SeoController::class, 'regenerateSitemap'])->name('dashboard.seo.sitemap.regenerate');
        
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
        Route::get('/backups/status', [Admin\BackupController::class, 'status'])->name('backups.status');
        Route::post('/backups', [Admin\BackupController::class, 'create'])->name('backups.create');
        Route::get('/backups/{id}/download', [Admin\BackupController::class, 'download'])->name('backups.download');
        Route::delete('/backups/{id}', [Admin\BackupController::class, 'destroy'])->name('backups.destroy');
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

        Route::resource('support-chats', Admin\SupportChatController::class)->only(['index', 'show']);
        Route::post('/support-chats/{support_chat}/send', [Admin\SupportChatController::class, 'sendMessage'])->name('support-chats.send');
        Route::post('/support-chats/{support_chat}/typing', [Admin\SupportChatController::class, 'typing'])->name('support-chats.typing');
        Route::post('/support-chats/{support_chat}/close', [Admin\SupportChatController::class, 'close'])->name('support-chats.close');
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
            Route::post('/media/order', [App\Http\Controllers\User\BusinessProfileController::class, 'updateMediaOrder'])->name('business.media.order');
            Route::put('/media/{id}/caption', [App\Http\Controllers\User\BusinessProfileController::class, 'updateMediaCaption'])->name('business.media.caption');
            Route::delete('/media/{id}', [App\Http\Controllers\User\BusinessProfileController::class, 'destroyMedia'])->name('business.media.destroy');

            // Leads CRM
            Route::get('/leads', [App\Http\Controllers\User\LeadController::class, 'index'])->name('dashboard.leads.index');
            Route::put('/leads/{id}/status', [App\Http\Controllers\User\LeadController::class, 'updateStatus'])->name('dashboard.leads.update');

            // Translations
            Route::get('/translations', [App\Http\Controllers\User\BusinessTranslationController::class, 'index'])->name('business.translations.index');
            Route::post('/translations', [App\Http\Controllers\User\BusinessTranslationController::class, 'update'])->name('business.translations.update');

            // Reviews
            Route::get('/reviews', [App\Http\Controllers\User\ReviewController::class, 'index'])->name('dashboard.reviews.index');
            Route::post('/reviews/{review}/reply', [App\Http\Controllers\User\ReviewController::class, 'reply'])->name('dashboard.reviews.reply');

            // Support Chat
            Route::get('/support/chat/fetch', [App\Http\Controllers\User\SupportChatController::class, 'fetchMessages'])->name('support.chat.fetch');
        });

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Notifications
        Route::prefix('notifications')->group(function () {
            Route::get('/unread', [App\Http\Controllers\User\NotificationController::class, 'fetchUnread'])->name('notifications.unread');
            Route::post('/{id}/read', [App\Http\Controllers\User\NotificationController::class, 'markAsRead'])->name('notifications.read');
            Route::post('/read-all', [App\Http\Controllers\User\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
        });

        Route::prefix('support-chat')->group(function () {
            Route::get('/', [\App\Http\Controllers\User\SupportChatController::class, 'index'])->name('support.chat.index');
            Route::get('/messages', [\App\Http\Controllers\User\SupportChatController::class, 'fetchMessages'])->name('support.chat.fetch');
            Route::post('/send', [\App\Http\Controllers\User\SupportChatController::class, 'sendMessage'])->name('support.chat.send');
            Route::post('/typing', [\App\Http\Controllers\User\SupportChatController::class, 'typing'])->name('support.chat.typing');
        });
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
Route::post('/chatbot/ask', [\App\Http\Controllers\ChatbotController::class, 'ask'])
    ->middleware('throttle:30,1')
    ->name('chatbot.ask');

Route::post('/{slug}/contact', [\App\Http\Controllers\PublicLeadController::class, 'store'])->name('directory.business.contact');
Route::post('/{slug}/reviews', [\App\Http\Controllers\PublicReviewController::class, 'store'])->name('directory.business.reviews.store');

// Dynamic Pages and Business Profiles fallbacks
Route::get('/{slug}', function ($slug) {
    // 1. Check if it's a dynamic page
    $page = \App\Models\Page::where('slug', $slug)->where('status', 'published')->first();
    if ($page) {
        return view('pages.show', compact('page'));
    }

    // 2. If not a page, pass to BusinessProfileController
    return app(\App\Http\Controllers\User\BusinessProfileController::class)->show($slug);
})->name('business.view');

Route::fallback(function () {
    abort(404);
});