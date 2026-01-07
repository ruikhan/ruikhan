<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                
                // ✅ NEW: Redirect authenticated users to their role-specific dashboard
                switch ($user->role) {
                    case 'admin':
                        return redirect()->route('admin.dashboard');
                    case 'business_owner':
                        return redirect()->route('business.dashboard');
                    case 'resident':
                    default:
                        return redirect()->route('dashboard');
                }
            }
        }

        return $next($request);
    }
}