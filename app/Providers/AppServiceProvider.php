<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->isLocal()) {
            URL::forceScheme('https');
        }

        // Rate limiting for web requests (60 requests per minute per IP)
        RateLimiter::for('web', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Stricter rate limiting for login attempts (5 attempts per minute)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // API rate limiting (if needed in future)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });
    }
}
