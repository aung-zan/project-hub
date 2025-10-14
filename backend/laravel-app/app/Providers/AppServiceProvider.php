<?php

namespace App\Providers;

use App\Guards\JWTGuard;
use App\Services\JWTService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;
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
        // Register JWTGuard as jwt for a new guard.
        Auth::extend('jwt', function (Application $app, string $_, array $config) {
            return new JWTGuard(
                Auth::createUserProvider($config['provider']),
                $app->make('request'),
                $app->make(JWTService::class)
            );
        });
    }
}
