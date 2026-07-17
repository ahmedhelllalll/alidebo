<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\FailedLink;

class LogFailedLinks
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->status() == 404 && !$request->is('api/*')) {
            $url = $request->path();
            // Don't log static assets if they accidentally hit this
            if (!preg_match('/\.(js|css|png|jpg|jpeg|gif|svg|ico|webp)$/i', $url)) {
                $failedLink = FailedLink::firstOrNew(['url' => $url]);
                $failedLink->hits = $failedLink->exists ? $failedLink->hits + 1 : 1;
                $failedLink->last_seen = now();
                $failedLink->save();
            }
        }

        return $response;
    }
}
