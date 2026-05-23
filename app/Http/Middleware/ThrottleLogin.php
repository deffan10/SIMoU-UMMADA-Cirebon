<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'login-attempt:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.");
        }

        RateLimiter::hit($key, 60);

        return $next($request);
    }
}
