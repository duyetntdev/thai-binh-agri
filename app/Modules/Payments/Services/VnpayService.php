<?php

namespace App\Modules\Payments\Services;

use App\Models\Order;
use Illuminate\Http\Request;

/**
 * VNPay payment gateway integration.
 * Docs: https://sandbox.vnpayment.vn/apis/docs/thanh-toan-pay/pay.html
 */
class VnpayService
{
    public function __construct(
        private readonly string $merchantId,
        private readonly string $secretKey,
        private readonly string $paymentUrl,
        private readonly string $returnUrl,
    ) {}

    /**
     * Build the VNPay redirect URL for an order.
     */
    public function buildPaymentUrl(Order $order, string $ipAddress): string
    {
        $params = [
            'vnp_Version'    => '2.1.0',
            'vnp_Command'    => 'pay',
            'vnp_TmnCode'    => $this->merchantId,
            'vnp_Amount'     => (int) ($order->total_amount * 100), // VNPay requires amount * 100
            'vnp_CurrCode'   => 'VND',
            'vnp_TxnRef'     => $order->id . '_' . time(),
            'vnp_OrderInfo'  => 'Thanh toan don hang #' . $order->id,
            'vnp_OrderType'  => 'other',
            'vnp_Locale'     => 'vn',
            'vnp_ReturnUrl'  => $this->returnUrl,
            'vnp_IpAddr'     => $ipAddress,
            'vnp_CreateDate' => now()->format('YmdHis'),
        ];

        ksort($params);

        $queryString = http_build_query($params);
        $signature   = hash_hmac('sha512', $queryString, $this->secretKey);

        return $this->paymentUrl . '?' . $queryString . '&vnp_SecureHash=' . $signature;
    }

    /**
     * Verify the callback signature from VNPay.
     */
    public function verifyCallback(Request $request): bool
    {
        $receivedHash = $request->query('vnp_SecureHash');

        $params = $request->except(['vnp_SecureHash', 'vnp_SecureHashType']);
        ksort($params);

        $queryString    = http_build_query($params);
        $expectedHash   = hash_hmac('sha512', $queryString, $this->secretKey);

        return hash_equals($expectedHash, $receivedHash ?? '');
    }

    /**
     * Check if the callback response code indicates success.
     */
    public function isSuccessful(Request $request): bool
    {
        return $request->query('vnp_ResponseCode') === '00';
    }

    /**
     * Extract the order ID from the TxnRef parameter.
     */
    public function extractOrderId(Request $request): int
    {
        $txnRef = $request->query('vnp_TxnRef', '');

        return (int) explode('_', $txnRef)[0];
    }
}
