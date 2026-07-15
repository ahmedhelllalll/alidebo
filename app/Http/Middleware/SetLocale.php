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
        $supportedLocales = ['en', 'ar', 'es', 'de', 'zh', 'tr'];

        // 1. Check if locale is provided in the route (e.g., /en/about)
        $routeLocale = $request->route('locale');

        if ($routeLocale && in_array($routeLocale, $supportedLocales)) {
            App::setLocale($routeLocale);
            Session::put('locale', $routeLocale);
            // Forget it so it doesn't get injected into controllers
            $request->route()->forgetParameter('locale');
        } else {
            // 2. Fallback to session or browser language if no route locale
            if (Session::has('locale')) {
                App::setLocale(Session::get('locale'));
            } else {
                $browserLanguage = $request->getPreferredLanguage($supportedLocales);
                if ($browserLanguage) {
                    App::setLocale($browserLanguage);
                    Session::put('locale', $browserLanguage);
                } else {
                    App::setLocale(config('app.locale', 'en'));
                }
            }
        }

        // Set the default locale for all route() generations globally
        \Illuminate\Support\Facades\URL::defaults(['locale' => App::getLocale()]);

        return $next($request);
    }
}
