<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Redirect;

class RedirectOldUrls
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check category redirects (both path-based and query-string based)
        $categoryRedirect = \App\Services\CategoryRedirectService::handleRequest($request);
        if ($categoryRedirect) {
            return $categoryRedirect;
        }

        $path = $request->path();
        $requestUri = ltrim($request->getRequestUri(), '/');
        $pathWithQuery = $path . ($request->getQueryString() ? '?' . $request->getQueryString() : '');

        // 2. Check exact path or path with query string in redirects table
        $redirect = Redirect::whereIn('source_url', [
            $path,
            '/' . ltrim($path, '/'),
            $requestUri,
            '/' . $requestUri,
            $pathWithQuery,
            '/' . $pathWithQuery,
        ])->first();

        if ($redirect) {
            return redirect($redirect->target_url, $redirect->status_code);
        }

        return $next($request);
    }
}

