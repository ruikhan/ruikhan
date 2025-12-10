<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsBusiness
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
public function handle(Request $request, Closure $next)
{
    // Check if the user is Logged In AND has the role 'business_owner'
    if (auth()->check() && auth()->user()->role === 'business_owner') {
        return $next($request); // Access Granted!
    }

    // Access Denied! Kick them back to dashboard with an error.
    abort(403, 'Unauthorized action. Only Business Owners can post jobs.');
}
}
