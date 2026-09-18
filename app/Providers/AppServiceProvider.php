<?php

namespace App\Providers;

use App\Contracts\PaymentGateway;
use App\Services\TestPaymentGateway;
use Illuminate\Support\Facades\Gate;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentGateway::class, fn () => $this->app->make(TestPaymentGateway::class));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', fn ($request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));
        Gate::before(function ($user, string $ability) {
            return $user->hasRole('super-administrator') ? true : null;
        });
    }
}
