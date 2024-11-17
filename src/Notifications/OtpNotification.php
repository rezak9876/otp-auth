<?php

namespace RezaK\Notifications\Notifications;

use Illuminate\Notifications\Notification;
use Rezak\SMSNotification\Messages\SMSMessage;

class OtpNotification extends Notification
{
    protected string $OTPCode;

    public function __construct(string $OTPCode)
    {
        $this->OTPCode = $OTPCode;
    }

    public function via(): array
    {
        return ['SMS'];
    }

    public function toSMS($notifiable): SMSMessage
    {
        return (new SMSMessage())
            ->setTemplate('mrlogistic')
            ->setData(['OTPCode' => $this->OTPCode]);
    }
}
