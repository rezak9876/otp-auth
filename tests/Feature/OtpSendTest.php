<?php
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use RezaK\OtpAuth\Http\Controllers\OtpController;
use Illuminate\Testing\Fluent\AssertableJson;
use RezaK\OtpAuth\Contracts\SMSSenderInterface;
use RezaK\OtpAuth\Contracts\MobileAuthInterface;
use Ichtrojan\Otp\Otp;

pest()->extend(Tests\TestCase::class);

beforeEach(function () {
    // Mock dependencies
    $this->mockMobileAuth = mock(MobileAuthInterface::class);
    app()->instance(MobileAuthInterface::class, $this->mockMobileAuth);

    $this->mockSmsSender = mock(SMSSenderInterface::class);
    app()->instance(SMSSenderInterface::class, $this->mockSmsSender);

    $this->otpMock = mock(Otp::class);
    app()->instance(Otp::class, $this->otpMock);
});



it('fails if mobile number is missing', function () {
    $response = $this->postJson('/api/auth/otp/send', [
        'mobile_number' => '',
    ]);
    $response->assertStatus(422)
             ->assertJsonValidationErrors('mobile_number');
});

it('fails if mobile number has less than 11 digits', function () {
    $response = $this->postJson('/api/auth/otp/send', [
        'mobile_number' => '0912603887',  // 10 digits instead of 11
    ]);
    $response->assertStatus(422)
             ->assertJsonValidationErrors('mobile_number');
});

it('fails if mobile number has more than 11 digits', function () {
    $response = $this->postJson('/api/auth/otp/send', [
        'mobile_number' => '091260388765',  // 12 digits instead of 11
    ]);
    $response->assertStatus(422)
             ->assertJsonValidationErrors('mobile_number');
});

it('fails if mobile number does not start with 09', function () {
    $response = $this->postJson('/api/auth/otp/send', [
        'mobile_number' => '08912345678',  // does not start with 09
    ]);
    $response->assertStatus(422)
             ->assertJsonValidationErrors('mobile_number');
});

it('fails if mobile number contains non-numeric characters', function () {
    $response = $this->postJson('/api/auth/otp/send', [
        'mobile_number' => '0912603887A',  // contains letter 'A'
    ]);
    $response->assertStatus(422)
             ->assertJsonValidationErrors('mobile_number');
});



















it('sends OTP successfully for valid mobile number', function () {
    $this->mockSmsSender
        ->shouldReceive('sendSMS')
        ->once()
        ->with('09128768852', '123456');

    $this->otpMock
        ->shouldReceive('generate')
        ->once()
        ->with('09128768852', 'numeric', 6)
        ->andReturn((object) ['token' => '123456']);

    $response = $this->postJson('/api/auth/otp/send', [
        'mobile_number' => '09128768852',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => __('otp-auth::messages.otp_sent'),
        ]);
});
