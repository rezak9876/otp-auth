<?php

namespace RezaK\Notifications\Http\Controllers;

use Carbon\Carbon;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use RezaK\Notifications\Contracts\MobileAuthInterface;
use RezaK\Notifications\Contracts\SMSSenderInterface;
use RezaK\Notifications\Http\Requests\SendOtpRequest;
use RezaK\Notifications\Http\Requests\VerifyOtpRequest;

class OtpController extends Controller
{
    protected Otp $otp;
    protected SMSSenderInterface $smsSender;
    protected MobileAuthInterface $mobileAuth;

    public function __construct(Otp $otp, SMSSenderInterface $smsSender, MobileAuthInterface $mobileAuth)
    {
        $this->otp = $otp;
        $this->smsSender = $smsSender;
        $this->mobileAuth = $mobileAuth;
    }

    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $token = $this->getGeneratedToken($request);
        $this->sendOtpSms($request->mobile_number, $token);

        return response()->json(['message' => __('otp-auth::messages.otp_sent')]);
    }

    /**
     * @param SendOtpRequest $request
     * @return mixed|object
     * @throws \Exception
     */
    protected function getGeneratedToken(SendOtpRequest $request): mixed
    {
        $existingOtp = $this->getValidOtp($request->mobile_number);

        return $existingOtp ? $existingOtp->token : $this->generateNewOtp($request->mobile_number);
    }

    protected function getValidOtp(string $mobileNumber): ?\Ichtrojan\Otp\Models\Otp
    {
        $existingOtp = \Ichtrojan\Otp\Models\Otp::where('identifier', $mobileNumber)
            ->where('valid', '1')
            ->first();

        if (!$existingOtp) {
            return null;
        }

        $validity = $existingOtp->created_at->addMinutes($existingOtp->validity);
        $now = Carbon::now();

        return strtotime($validity) >= strtotime($now) ? $existingOtp : null;
    }

    protected function generateNewOtp(string $mobileNumber): string
    {
        return $this->otp->generate($mobileNumber, 'numeric', 6)->token;
    }

    protected function sendOtpSms(string $mobileNumber, string $token): void
    {
        $this->smsSender->sendSMS($mobileNumber, $token);
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
}
