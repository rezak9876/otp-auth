<?php
namespace RezaK\OtpAuth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use RezaK\OtpAuth\Rules\MobileNumber;

class VerifyOtpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'otp' => 'bail|required|string|digits:6',
            'mobile_number' => ['required', new MobileNumber],
        ];
    }
}
