<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;

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
               ResetPassword::createUrlUsing(function ($notifiable, $token) {
            return 'http://localhost:5173/redefinir-senha/' . $token . '?email=' . urlencode($notifiable->getEmailForPasswordReset());
        });
    }
}
