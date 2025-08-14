<?php

return [
    'merchant_id'  => env('YAGOUT_MERCHANT_ID', ''),
    'merchant_key' => env('YAGOUT_MERCHANT_KEY', ''),
    'payment_url'  => env('YAGOUT_PAYMENT_URL', 'https://sandbox.yagoutpay.com/initiate'),
    'success_url'  => env('YAGOUT_SUCCESS_URL', '/yagoutpay/success'),
    'failure_url'  => env('YAGOUT_FAILURE_URL', '/yagoutpay/failure')
];
