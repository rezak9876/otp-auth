<?php
namespace RezaK\Notifications\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use RezaK\Notifications\Rules\PhoneNumber;

class SendOtpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'mobile_number' => ['required', new PhoneNumber],
        ];
    }
}
