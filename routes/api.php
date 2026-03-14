<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are for external / token-based (Sanctum) consumers such as
| a mobile app.  DO NOT add notification or any session-dependent routes
| here — they live in routes/web.php under the `auth` + `verified` group
| so that CSRF and session middleware are applied consistently.
|
*/

// Standard Sanctum identity endpoint
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ⚠️  REMOVED: Notification routes were previously duplicated here AND in
// web.php.  Because web.php routes are registered first, the web middleware
// (including VerifyCsrfToken) always won, causing 419 on POST requests from
// the SPA.  All /api/notifications/* routes now live exclusively in web.php
// under the session-auth middleware group.