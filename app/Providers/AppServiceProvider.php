<?php

namespace App\Providers;

use App\Auth\CustomSessionGuard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Pagination\Paginator;
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
        Paginator::useBootstrapFive();

        Auth::extend('custom-session', function (Application $app, string $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);

            $guard = new CustomSessionGuard(
                $name,
                $provider,
                $app['session.store'],
                $app['request']
            );

            $guard->setCookieJar($app['cookie']);

            return $guard;
        });
    }
}
