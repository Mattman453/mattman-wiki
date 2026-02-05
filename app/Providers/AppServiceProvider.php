<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        RateLimiter::for('global', function (Request $request) {
            if ($request->user()) {
                if (isset($request->user()->roles) && (in_array('admin', $request->user()->roles) || in_array('author', $request->user()->roles))) return Limit::none();
                return Limit::perMinute(300)->by($request->user()->id());
            }
            return Limit::perMinute(60)->by($request->ip());
        });

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
