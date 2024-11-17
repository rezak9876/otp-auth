<?php
namespace RezaK\Notifications\Listeners;


use Illuminate\Support\Facades\Notification;
use RezaK\Auth\Events\OtpGenerated;
use RezaK\Notifications\Notifications\OtpNotification;

class SendOtpNotification
{
    public function handle(OtpGenerated $event)
    {
        Notification::route('SMS', $event->mobileNumber)->notify(new OtpNotification($event->otpCode));
    }
}
