<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // ✅ NEW: Store the intended URL so we can redirect back after login
            if ($request->url() !== route('login')) {
                session(['url.intended' => $request->url()]);
            }
            
            return route('login');
        }
    }
}