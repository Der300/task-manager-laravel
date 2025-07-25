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
                'roleAboveManager' => $user ? $user->hasAnyRole(['admin', 'super-admin']) : false,
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
            $roleAboveManager = $user ? $user->hasAnyRole(['admin', 'super-admin']) : false;

            $canResetPassword = function ($item) use ($user) {
                return $user && (
                    $user->hasAnyRole(['admin', 'super-admin']) ||
                    ($user->department === $item->department & $user->hasAnyRole(['manager', 'leader']) & $user->id !== $item->id)
                );
            };

            $canManageUser = function ($item) use ($user) {
                $level = [
                    'super-admin' => 4,
                    'admin'       => 3,
                    'manager'     => 2,
                    'leader'      => 1,
                    'member'      => 0,
                ];

                $userRoleLevel  = $level[$user->role] ?? -1;
                $itemRoleLevel  = $level[$item->role] ?? -1;

                if ($user->id === $item->id) return false;

                return $userRoleLevel > $itemRoleLevel;
            };

            $canSeeProfile  = function ($item) use ($user) {
                if (!$user || !$item) return false;

                if ($user->hasRole('super-admin')) {
                    return true;
                }

                if ($user->hasRole('admin')) {
                    if ($user->id === $item->id) return true;

                    if (!in_array($item->role, ['admin', 'super-admin'])) return true;

                    return false;
                }

                return $user->id === $item->id;
            };


            $view->with([
                'department' => $user ? $user->department : null,
                'roleAboveManager' => $roleAboveManager,
                'roleAboveMember' => $user ? $user->hasAnyRole(['admin', 'super-admin', 'manager', 'leader']) : false,
                'canResetPassword' => $canResetPassword,
                'canManageUser' => $canManageUser,
                'canSeeProfile' => $canSeeProfile,
            ]);
        });
    }
}
