<?php

namespace RezaK\OtpAuth\Contracts;

interface MobileAuthInterface
{
    /**
     * @param string $mobileNumber
     * @return string $token
     */
    public function generateToken(string $mobileNumber): string;
}