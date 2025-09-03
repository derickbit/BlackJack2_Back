<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registra o DuskServiceProvider apenas no ambiente local

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
               if($this->app->environment('production')) {
            URL::forceScheme('https');
        }

    RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());

    });

               ResetPassword::createUrlUsing(function ($notifiable, $token) {
            return 'http://localhost:5173/redefinir-senha/' . $token . '?email=' . urlencode($notifiable->getEmailForPasswordReset());
        });
    }
}
