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

        view()->composer('dashboard', function ($view) {
            $user = Auth::user();
            $view->with([
                'roleNotClient' => $user ? !$user->hasRole('client') : false,
                'roleAboveMember' => $user ? $user->hasAnyRole(['admin', 'super-admin', 'manager', 'leader']) : false,
            ]);
        });

        view()->composer('users.*', function ($view) {
            $user = Auth::user();
            $canResetPassword = function ($item) use ($user) {
                return $user && (
                    $user->hasAnyRole(['admin', 'super-admin']) ||
                    ($user->department === $item->department & $user->hasAnyRole(['manager', 'leader']))
                );
            };

            $canManageUser = function ($item) use ($user) {
                if (!$user || !$item) return false;

                if ($user->hasRole('super-admin') && $item->role !== 'super-admin') {
                    return true;
                }
                
                if ($user->hasRole('admin') && !in_array($item->role, ['admin', 'super-admin'])) {
                    return true;
                }

                return false;
            };

            $view->with([
                'department' => $user ? $user->department : null,
                'roleAboveManager' => $user ? $user->hasAnyRole(['admin', 'super-admin']) : false,
                'roleAboveMember' => $user ? $user->hasAnyRole(['admin', 'super-admin', 'manager', 'leader']) : false,
                'canResetPassword' => $canResetPassword,
                'canManageUser' => $canManageUser,
            ]);
        });
    }
}
