<?php

namespace RezaK\OtpAuth\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class MobileNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (!preg_match('/^09\d{9}$/', $value) || strlen($value) < 10 || strlen($value) > 15) {
            $fail('The :attribute must be a valid phone number with 10 to 15 digits starting with 09.');
        }
    }
}
