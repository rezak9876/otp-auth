<?php
namespace RezaK\OtpAuth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use RezaK\OtpAuth\Rules\PhoneNumber;

class VerifyOtpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'otp' => 'required|digits:6',
            'mobile_number' => ['required', new PhoneNumber],
        ];
    }
}
