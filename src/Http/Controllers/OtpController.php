<?php

namespace RezaK\OtpAuth\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Routing\Controller;
use RezaK\OtpAuth\Contracts\SMSSenderInterface;
use RezaK\OtpAuth\Contracts\MobileAuthInterface;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\JsonResponse;
use RezaK\OtpAuth\Http\Requests\SendOtpRequest;
use RezaK\OtpAuth\Http\Requests\VerifyOtpRequest;

class OtpController extends Controller
{
    protected Otp $otp;
    protected SMSSenderInterface $smsSender;
    protected MobileAuthInterface $mobileAuth;

    public function __construct(Otp $otp, SMSSenderInterface $smsSender,MobileAuthInterface $mobileAuth)
    {
        $this->otp = $otp;
        $this->smsSender = $smsSender;
        $this->mobileAuth = $mobileAuth;
    }

    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $otpDetails = $this->otp->generate($request->mobile_number, 'numeric', 6);
        $this->sendOtpSms($request->mobile_number, $otpDetails->token);

        return response()->json(['message' => __('otp-auth::messages.otp_sent')]);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        if (!$this->isValidOtp($request->mobile_number, $request->otp)) {
            return response()->json(['message' => __('otp-auth::messages.invalid_otp')], 400);
        }

        $token = $this->mobileAuth->generateToken($request->mobile_number);

        return response()->json([
            'message' => __('otp-auth::messages.logged_in'),
            'token' => $token,
        ]);
    }

    protected function isValidOtp(string $mobileNumber, string $otp): bool
    {
        return $this->otp->validate($mobileNumber, $otp)->status;
    }

    protected function sendOtpSms(string $mobileNumber, string $token): void
    {
        $this->smsSender->sendSMS($mobileNumber, $token);
    }
}
