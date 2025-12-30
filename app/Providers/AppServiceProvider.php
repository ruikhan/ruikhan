<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <--- ADD THIS LINE

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
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
        // FORCE HTTPS IN PRODUCTION
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}