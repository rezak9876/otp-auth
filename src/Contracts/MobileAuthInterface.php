<?php

namespace RezaK\Notifications\Contracts;

interface MobileAuthInterface
{
    /**
     * @param string $mobileNumber
     * @return string $token
     */
    public function generateToken(string $mobileNumber): string;
}