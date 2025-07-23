<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        view()->composer('dashboard', function ($view) {
            $user = Auth::user();
            $view->with([
                'roleNotClient' => $user ? !$user->hasRole('client') : false,
                'roleAboveMember' => $user ? $user->hasAnyRole(['admin', 'super-admin', 'manager', 'leader']) : false,
            ]);
        });

        view()->composer('partials.main_sidebar', function ($view) {
            $user = Auth::user();
            $view->with([
                'roleNotClient' => $user ? !$user->hasRole('client') : false,
                'roleAdminOrSuper'  => $user ? $user->hasAnyRole(['admin', 'super-admin']) : false,
                'roleAboveLeader' => $user ? $user->hasAnyRole(['admin', 'super-admin', 'manager']) : false,
                'roleAboveMember' => $user ? $user->hasAnyRole(['admin', 'super-admin', 'manager', 'leader']) : false,
            ]);
        });

        view()->composer('partials.main_navbar', function ($view) {
            $user = Auth::user();
            $notifications = $user ? $user->unreadNotifications : collect();

            $view->with([
                'notifications' => $notifications,
            ]);
        });
    }
}
