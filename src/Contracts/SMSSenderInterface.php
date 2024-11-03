<?php

namespace RezaK\OtpAuth\Contracts;

interface SMSSenderInterface
{
    public function sendSms(string $mobileNumber, string $message): void;
}
