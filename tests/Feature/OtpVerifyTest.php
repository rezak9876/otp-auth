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



it('fails if OTP is missing', function () {
    $response = $this->postJson('/api/auth/otp/verify', [
        'otp' => '',  // OTP خالی
        'mobile_number' => '09126038876',
    ]);
    $response->assertStatus(422)
             ->assertJsonValidationErrors('otp');
});

it('fails if OTP is not in the correct format', function () {
    // OTP باید حتماً شش رقم باشد
    $response = $this->postJson('/api/auth/otp/verify', [
        'otp' => '1234',  // OTP اشتباه (کمتر از شش رقم)
        'mobile_number' => '09126038876',
    ]);
    $response->assertStatus(422)
             ->assertJsonValidationErrors('otp');
});


it('fails if mobile number is missing', function () {
    // شماره موبایل نباید خالی باشد
    $response = $this->postJson('/api/auth/otp/verify', [
        'otp' => '123456',
        'mobile_number' => '',  // شماره موبایل خالی
    ]);
    $response->assertStatus(422)
             ->assertJsonValidationErrors('mobile_number');
});

it('fails if mobile number is not in the correct format', function () {
    // شماره موبایل باید به فرمت صحیح باشد
    $response = $this->postJson('/api/auth/otp/verify', [
        'otp' => '123456',
        'mobile_number' => '0912603887',  // شماره موبایل اشتباه (کمتر از ده رقم)
    ]);
    $response->assertStatus(422)
             ->assertJsonValidationErrors('mobile_number');
});


it('fails to verify OTP for invalid OTP', function () {
    $this->otpMock
        ->shouldReceive('validate')
        ->once()
        ->andReturn((object) ['status' => false]);

    $response = $this->postJson('/api/auth/otp/verify', [
        'mobile_number' => '09128768852',
        'otp' => '234532',
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'message' => __('otp-auth::messages.invalid_otp'),
        ]);
});

it('verifies correct OTP', function () {
    $this->mockMobileAuth
        ->shouldReceive('generateToken')
        ->once()
        ->with('09128768852')
        ->andReturn('randomString123');

    $this->otpMock
        ->shouldReceive('validate')
        ->once()
        ->with('09128768852', '123456')
        ->andReturn((object) ['status' => true]);

    $response = $this->postJson('/api/auth/otp/verify', [
        'mobile_number' => '09128768852',
        'otp' => '123456',
    ]);

    $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('message', __('otp-auth::messages.logged_in'))
                ->where('token', fn ($token) => strlen($token) > 0)
        );
});
