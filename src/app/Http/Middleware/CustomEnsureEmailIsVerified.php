<?php

// app/Http/Middleware/CustomEnsureEmailIsVerified.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CustomEnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->hasVerifiedEmail()) {
            if ($request->is('login') || $request->is('register')) {
                return $next($request);
            }
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
