# OTP Authentication Package for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rezak/otp-auth.svg?style=flat-square)](https://packagist.org/packages/rezak/otp-auth)
[![Total Downloads](https://img.shields.io/packagist/dt/rezak/otp-auth.svg?style=flat-square)](https://packagist.org/packages/rezak/otp-auth)

This package provides an OTP-based authentication system for Laravel applications, including SMS-based OTP generation and verification.

## Features
- Generate OTPs and send via SMS
- Verify OTPs for authentication
- Easy to integrate into any Laravel application

## Installation

You can install the package via composer:

```bash
composer require rezak/otp-auth
```

## Configuration

1. Publish the configuration and language files:

```bash
php artisan vendor:publish --provider="RezaK\OtpAuth\Providers\OtpAuthServiceProvider"
```

2. Add your SMS gateway service to the `SMSSenderInterface` implementation.

## Usage

### Routes

The package automatically registers the following routes:

- **POST** `/api/auth/otp/send` - Send OTP to a mobile number.
- **POST** `/api/auth/otp/verify` - Verify OTP for a mobile number.

### Example Request

#### Sending OTP
```bash
curl -X POST \
  -H "Content-Type: application/json" \
  -d '{"mobile_number": "09123456789"}' \
  http://your-app-url/api/auth/otp/send
```

#### Verifying OTP
```bash
curl -X POST \
  -H "Content-Type: application/json" \
  -d '{"mobile_number": "09123456789", "otp": "123456"}' \
  http://your-app-url/api/auth/otp/verify
```

## Testing

Run the tests with:

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
