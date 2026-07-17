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
        $path = $request->path();
        
        // Check exact path or path with leading slash
        $redirect = Redirect::where('source_url', $path)
            ->orWhere('source_url', '/' . ltrim($path, '/'))
            ->first();

        if ($redirect) {
            return redirect($redirect->target_url, $redirect->status_code);
        }

        return $next($request);
    }
}
