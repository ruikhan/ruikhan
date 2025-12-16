<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasBusiness
{
    /**
     * Ensure user has a business AND a valid subscription.
     * Used to protect /business/dashboard and related routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Check #1: Does user have a business?
        if (!$user->business) {
            return redirect()->route('business.register')
                ->with('info', '⚠️ Please register your business first.');
        }

        // Check #2: Is their subscription still valid?
        if (!$user->hasActiveSubscription()) {
            return redirect()->route('business.register')
                ->with('warning', '⏰ Your subscription has expired. Please renew to continue.');
        }

        // All checks passed - allow access
        return $next($request);
    }
}