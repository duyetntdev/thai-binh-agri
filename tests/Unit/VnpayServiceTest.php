<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Modules\Payments\Services\VnpayService;
use Illuminate\Http\Request;
use Tests\TestCase;

class VnpayServiceTest extends TestCase
{
    private const SECRET = 'test-secret';

    public function test_callback_accepts_valid_signature_and_rejects_tampered_parameters(): void
    {
        $service = $this->service();
        $params = [
            'vnp_TxnRef' => '42_123456',
            'vnp_ResponseCode' => '00',
            'vnp_TransactionNo' => '987654',
        ];
        $params['vnp_SecureHash'] = $this->signature($params);

        $this->assertTrue($service->verifyCallback(Request::create('/callback', 'GET', $params)));

        $params['vnp_ResponseCode'] = '24';

        $this->assertFalse($service->verifyCallback(Request::create('/callback', 'GET', $params)));
    }

    public function test_callback_extracts_order_id_and_success_response(): void
    {
        $request = Request::create('/callback', 'GET', [
            'vnp_TxnRef' => '42_123456',
            'vnp_ResponseCode' => '00',
        ]);

        $this->assertSame(42, $this->service()->extractOrderId($request));
        $this->assertTrue($this->service()->isSuccessful($request));
    }

    public function test_payment_url_contains_amount_and_a_valid_signature(): void
    {
        $this->travelTo(now()->setDate(2026, 5, 20)->setTime(10, 30));
        $order = new Order(['total_amount' => 125.50]);
        $order->id = 42;

        $url = $this->service()->buildPaymentUrl($order, '127.0.0.1');
        parse_str((string) parse_url($url, PHP_URL_QUERY), $params);
        $receivedSignature = $params['vnp_SecureHash'];
        unset($params['vnp_SecureHash']);
        ksort($params);

        $this->assertSame(12550, (int) $params['vnp_Amount']);
        $this->assertSame('42_'.time(), $params['vnp_TxnRef']);
        $this->assertSame($this->signature($params), $receivedSignature);
    }

    private function service(): VnpayService
    {
        return new VnpayService(
            merchantId: 'merchant',
            secretKey: self::SECRET,
            paymentUrl: 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
            returnUrl: 'https://example.test/callback',
        );
    }

    private function signature(array $params): string
    {
        unset($params['vnp_SecureHash'], $params['vnp_SecureHashType']);
        ksort($params);

        return hash_hmac('sha512', http_build_query($params), self::SECRET);
    }
}
