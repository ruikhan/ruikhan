<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator; // ✅ ADD: For pagination styling
use Inertia\Inertia; // ✅ ADD: For Inertia shared data

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register ProfileService as singleton
        $this->app->singleton(\App\Services\ProfileService::class, function ($app) {
            return new \App\Services\ProfileService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // ✅ FORCE HTTPS IN PRODUCTION
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // ✅ USE TAILWIND CSS FOR PAGINATION (instead of Bootstrap)
        Paginator::useTailwind();

        // ✅ SHARE FLASH MESSAGES WITH ALL INERTIA PAGES
        Inertia::share([
            'flash' => function () {
                return [
                    'success' => session('success'),
                    'error' => session('error'),
                    'message' => session('message'),
                ];
            },
        ]);
    }
}