<?php

namespace RezaK\Notifications\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use RezaK\Auth\Events\OtpGenerated;
use RezaK\Notifications\Listeners\SendOtpNotification;

// Import your package's listener

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        OtpGenerated::class => [
            SendOtpNotification::class,
        ],
    ];
}