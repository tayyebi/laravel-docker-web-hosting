<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Force the URL to use the APP_URL environment variable
        URL::forceRootUrl(config('app.url'));

        // // Optional: Force HTTPS if required
        // if (config('app.env') !== 'local') {
        //     URL::forceScheme('https');
        // }
    }
}
