# YagoutPay Laravel Package

[![Latest Version](https://img.shields.io/packagist/v/miki-babi/yagoutpay.svg?style=flat-square)](https://packagist.org/packages/miki-babi/yagoutpay)
[![License](https://img.shields.io/packagist/l/miki-babi/yagoutpay.svg?style=flat-square)](https://packagist.org/packages/miki-babi/yagoutpay)

A comprehensive Laravel package for seamless integration with the **YagoutPay Payment Gateway**. This package provides a clean, secure, and easy-to-use interface for processing payments through YagoutPay's API.

## ✨ Features

- 🔒 **Secure AES-256-CBC Encryption** - Built-in encryption for payment data
- 🎯 **Laravel Integration** - Native Laravel service provider and facade
- 🛡️ **Environment-based Configuration** - Secure credential management
- 📝 **Comprehensive Logging** - Built-in logging for debugging and monitoring
- 🔄 **Callback Handling** - Ready-to-use success/failure callback routes
- 🧪 **Testing Support** - Sandbox environment support

## 📋 Requirements

- **PHP** >= 8.0
- **Laravel** >= 10.0
- **OpenSSL** extension enabled

## 📦 Installation

### 1. Install via Composer
```bash
composer require miki-babi/yagoutpay
```

### 2. Publish Configuration
```bash
php artisan vendor:publish --tag=yagoutpay-config
```

### 3. Environment Configuration
Add the following variables to your `.env` file:

```env
# YagoutPay Configuration
YAGOUT_MERCHANT_ID=your_merchant_id
YAGOUT_MERCHANT_KEY=your_merchant_key
YAGOUT_PAYMENT_URL=https://sandbox.yagoutpay.com/initiate

# Optional: Custom callback URLs
YAGOUT_SUCCESS_URL=https://yoursite.com/payment/success
YAGOUT_FAILURE_URL=https://yoursite.com/payment/failure
```

## 🚀 Usage

### Basic Payment Initiation

```php
use MikiBabi\YagoutPay\Facades\Yagout;

// Initialize payment
$paymentForm = Yagout::initiate(
    orderId: 'ORDER_' . time(),
    amount: 150.00,
    customerDetails: [
        'name'  => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '0912345678'
    ],
    currency: 'ETB', // Optional, defaults to 'ETB'
    transactionType: 'SALE', // Optional, defaults to 'SALE'
    successUrl: route('payment.success'), // Optional
    failureUrl: route('payment.failure')  // Optional
);

// The method returns a Blade view that auto-submits to YagoutPay
return $paymentForm;
```

### Controller Example

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MikiBabi\YagoutPay\Facades\Yagout;

class PaymentController extends Controller
{
    public function initiatePayment(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:20',
        ]);

        $orderId = 'ORDER_' . uniqid();
        
        return Yagout::initiate(
            orderId: $orderId,
            amount: $validated['amount'],
            customerDetails: [
                'name'  => $validated['customer_name'],
                'email' => $validated['customer_email'],
                'phone' => $validated['customer_phone']
            ]
        );
    }

    public function paymentSuccess(Request $request)
    {
        // Handle successful payment
        $callbackData = $request->all();
        
        // Process your business logic here
        // e.g., update order status, send confirmation email
        
        return view('payment.success', compact('callbackData'));
    }

    public function paymentFailure(Request $request)
    {
        // Handle failed payment
        $callbackData = $request->all();
        
        // Process failure logic here
        // e.g., log error, notify user
        
        return view('payment.failure', compact('callbackData'));
    }
}
```

### Route Configuration

```php
// routes/web.php
use App\Http\Controllers\PaymentController;

Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment'])
    ->name('payment.initiate');

Route::post('/payment/success', [PaymentController::class, 'paymentSuccess'])
    ->name('payment.success')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::post('/payment/failure', [PaymentController::class, 'paymentFailure'])
    ->name('payment.failure')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
```

## ⚙️ Configuration

The configuration file `config/yagoutpay.php` contains the following options:

```php
return [
    'merchant_id'  => env('YAGOUT_MERCHANT_ID', ''),
    'merchant_key' => env('YAGOUT_MERCHANT_KEY', ''),
    'payment_url'  => env('YAGOUT_PAYMENT_URL', 'https://sandbox.yagoutpay.com/initiate'),
    'success_url'  => env('YAGOUT_SUCCESS_URL', url('payment/success')),
    'failure_url'  => env('YAGOUT_FAILURE_URL', url('payment/failure'))
];
```

## 🧪 Testing

### Sandbox Environment
For testing, use the sandbox URL:
```env
YAGOUT_PAYMENT_URL=https://sandbox.yagoutpay.com/initiate
```

### Test Credentials
Contact YagoutPay support to obtain sandbox credentials for testing.

## 🔒 Security

- All payment data is encrypted using AES-256-CBC encryption
- Merchant credentials are stored securely in environment variables
- CSRF protection is automatically disabled for callback routes
- All transactions are logged for audit purposes

## 🐛 Troubleshooting

### Common Issues

1. **Encryption Errors**: Ensure your `YAGOUT_MERCHANT_KEY` is properly base64 encoded
2. **Callback Issues**: Make sure callback URLs are publicly accessible
3. **Configuration**: Verify all environment variables are set correctly

### Logging
Check Laravel logs for detailed error information:
```bash
tail -f storage/logs/laravel.log
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 Changelog

See [CHANGELOG.md](CHANGELOG.md) for details on version changes.

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## 🆘 Support

- **Documentation**: [GitHub Wiki](https://github.com/miki-babi/yagoutpay/wiki)
- **Issues**: [GitHub Issues](https://github.com/miki-babi/yagoutpay/issues)
- **Email**: mikiyasshiferaw99@gmail.com

---
