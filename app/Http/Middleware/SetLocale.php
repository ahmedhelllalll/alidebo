<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            // Detect preferred language from the browser's Accept-Language header
            $supportedLocales = ['en', 'ar', 'es', 'de', 'zh', 'tr'];
            $browserLanguage = $request->getPreferredLanguage($supportedLocales);

            if ($browserLanguage) {
                App::setLocale($browserLanguage);
                Session::put('locale', $browserLanguage); // Save it so we don't have to parse the header every time
            } else {
                App::setLocale(config('app.locale', 'en'));
            }
        }

        return $next($request);
    }
}
