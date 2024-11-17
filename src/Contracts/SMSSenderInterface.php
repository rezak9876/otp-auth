<?php

namespace RezaK\Notifications\Contracts;

interface SMSSenderInterface
{
    public function sendSms(string $mobileNumber, string $OTPCode): void;
}
