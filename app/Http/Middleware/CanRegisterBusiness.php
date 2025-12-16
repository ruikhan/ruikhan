<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanRegisterBusiness
{
    /**
     * Check if user is allowed to register a business.
     * Prevents: Admins, users with existing active businesses.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Prevent #1: Admins cannot register businesses
        if ($user->role === 'admin') {
            return redirect()->route('dashboard')
                ->with('error', '🚫 Administrators cannot register businesses.');
        }

        // Prevent #2: Users who already have an active business
        if ($user->business && $user->hasActiveSubscription()) {
            return redirect()->route('business.dashboard')
                ->with('info', '✅ You already have an active business.');
        }

        // Allow registration if:
        // - User has no business, OR
        // - User has a business but subscription expired (renewal)
        return $next($request);
    }
}