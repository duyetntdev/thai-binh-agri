<?php

return [
    'merchant_id' => env('VNPAY_MERCHANT_ID', ''),
    'secret_key'  => env('VNPAY_SECRET_KEY', ''),
    'url'         => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paygate'),
    'return_url'  => env('VNPAY_RETURN_URL', 'http://localhost:8000/payment/callback'),
];
