<?php
namespace RezaK\Notifications\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use RezaK\Notifications\Rules\PhoneNumber;

class VerifyOtpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'otp' => 'bail|required|string|digits:6',
            'mobile_number' => ['required', new PhoneNumber],
        ];
    }
}
