<?php
namespace RezaK\Notifications\Rules;

use Illuminate\Contracts\Validation\Rule;

class PhoneNumber implements Rule
{
    public function __construct()
    {
        // Any setup can go here, if needed
    }

    public function passes($attribute, $value): bool
    {
        // Check if the phone number matches the required regex and length
        return preg_match('/^09\d{9}$/', $value) && strlen($value) >= 10 && strlen($value) <= 15;
    }

    public function message(): string
    {
        // Custom error message
        return 'The :attribute must be a valid phone number with 10 to 15 digits starting with 09.';
    }
}
