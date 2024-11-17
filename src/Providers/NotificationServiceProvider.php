<?php

namespace RezaK\Notifications\Providers;

use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->register(EventServiceProvider::class);
    }
}
