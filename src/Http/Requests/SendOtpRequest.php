<?php
namespace RezaK\OtpAuth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use RezaK\OtpAuth\Rules\MobileNumber;

class SendOtpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'mobile_number' => ['required', new MobileNumber],
        ];
    }
}
