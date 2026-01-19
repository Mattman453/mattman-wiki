<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
        Gate::before(function (User $user, string $ability) {
            if (!isset($user->roles)) return false;
            return in_array('admin', $user->roles);
        });
        
        Gate::define('author', function (User $user) {
            if (!isset($user->roles)) return false;
            return in_array('author', $user->roles) || in_array('admin', $user->roles);
        });
    }
}
