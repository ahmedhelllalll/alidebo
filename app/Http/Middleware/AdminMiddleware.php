<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->isAdmin()) {
            return $next($request);
        }

        // If user tries to access admin dashboard, redirect to user dashboard
        if ($request->user() && $request->user()->isUser()) {
            return redirect()->route('dashboard');
        }

        return redirect('/');
    }
}

