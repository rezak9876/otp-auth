<?php

namespace RezaK\OtpAuth\Providers;

use Illuminate\Support\ServiceProvider;

class OtpAuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');

        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'otp-auth');
    }
}
