<?php

namespace RezaK\OtpAuth\Http\Controllers;

use Carbon\Carbon;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use RezaK\OtpAuth\Contracts\MobileAuthInterface;
use RezaK\OtpAuth\Contracts\SMSSenderInterface;
use RezaK\OtpAuth\Http\Requests\SendOtpRequest;
use RezaK\OtpAuth\Http\Requests\VerifyOtpRequest;


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

    /**
     * Send an OTP to the specified mobile number.
     *
     * @OA\Post(
     *     path="/auth/otp/send",
     *     summary="Send OTP",
     *     description="Sends an OTP to the provided mobile number.",
     *     tags={"OTP"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="mobile_number",
     *                 type="string",
     *                 example="09123456789",
     *                 description="validation rules: required|MobileNumber"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP has been sent successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid mobile number format.")
     *         )
     *     )
     * )
     */
    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $token = $this->getGeneratedToken($request);
        $this->sendOtpSms($request->mobile_number, $token);

        return response()->json(['message' => __('otp-auth::messages.otp_sent')]);
    }


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

    /**
     * Verify the provided OTP.
     *
     * @OA\Post(
     *     path="/auth/otp/verify",
     *     summary="Verify OTP",
     *     description="Verifies the OTP for the provided mobile number.",
     *     tags={"OTP"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="mobile_number",
     *                 type="string",
     *                 example="09123456789",
     *                 description="validation rules: 'required'|MobileNumber'"
     *             ),
     *             @OA\Property(
     *                 property="otp",
     *                 type="string",
     *                 example="123456",
     *                 description="validation rules: 'bail|required|string|digits:6'"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged in."),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid OTP or mobile number.")
     *         )
     *     )
     * )
     */
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
