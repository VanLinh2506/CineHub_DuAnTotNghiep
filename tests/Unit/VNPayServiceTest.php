<?php

namespace Tests\Unit;

use App\Services\VNPayService;
use Illuminate\Http\Request;
use Tests\TestCase;

class VNPayServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        config()->set('services.vnpay.tmn_code', 'TESTCODE');
        config()->set('services.vnpay.hash_secret', 'test-secret');
    }

    public function test_payment_url_normalizes_xampp_ipv6_loopback(): void
    {
        $url = app(VNPayService::class)->createPaymentUrl(
            txnRef: 'DEP1_123',
            amount: 10000,
            orderInfo: 'Nap 10000 diem',
            returnUrl: 'http://localhost/profile/vnpay-deposit-return',
            clientIp: '::1'
        );

        parse_str((string) parse_url($url, PHP_URL_QUERY), $params);

        $this->assertSame('127.0.0.1', $params['vnp_IpAddr']);
        $this->assertSame('1000000', $params['vnp_Amount']);
        $this->assertNotEmpty($params['vnp_SecureHash']);
    }

    public function test_callback_signature_ignores_non_vnp_query_parameters(): void
    {
        $params = [
            'vnp_Amount' => '1000000',
            'vnp_ResponseCode' => '00',
            'vnp_TxnRef' => 'DEP1_123',
        ];
        ksort($params);
        $params['vnp_SecureHash'] = hash_hmac('sha512', http_build_query($params), 'test-secret');
        $params['utm_source'] = 'browser';

        $request = Request::create('/profile/vnpay-deposit-return', 'GET', $params);

        $this->assertTrue(app(VNPayService::class)->verifyCallback($request));
    }

    public function test_callback_rejects_tampered_vnp_data(): void
    {
        $signed = [
            'vnp_Amount' => '1000000',
            'vnp_ResponseCode' => '00',
            'vnp_TxnRef' => 'DEP1_123',
        ];
        ksort($signed);
        $hash = hash_hmac('sha512', http_build_query($signed), 'test-secret');
        $signed['vnp_Amount'] = '2000000';
        $signed['vnp_SecureHash'] = $hash;

        $request = Request::create('/profile/vnpay-deposit-return', 'GET', $signed);

        $this->assertFalse(app(VNPayService::class)->verifyCallback($request));
    }
}
