<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        if (!auth()->guard('admin')->user()->is_active) {
            auth()->guard('admin')->logout();
            return redirect()->route('admin.login')->with('error', 'Akun Anda tidak aktif.');
        }

        return $next($request);
    }
}
