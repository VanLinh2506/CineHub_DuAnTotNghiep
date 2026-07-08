<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Http\Request;
use RuntimeException;

class VNPayService
{
    protected string $vnpUrl;
    protected string $tmnCode;
    protected string $hashSecret;

    public function __construct()
    {
        $this->vnpUrl = config('services.vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $this->tmnCode = (string) config('services.vnpay.tmn_code');
        $this->hashSecret = (string) config('services.vnpay.hash_secret');
    }

    public function createBookingPaymentUrl(
        Booking $booking,
        string $orderInfo,
        string $returnUrl,
        string $clientIp
    ): string {
        return $this->createPaymentUrl(
            txnRef: $booking->vnp_txn_ref,
            amount: (float) $booking->total_amount,
            orderInfo: $orderInfo,
            returnUrl: $returnUrl,
            clientIp: $clientIp
        );
    }

    public function createPaymentUrl(
        string $txnRef,
        float $amount,
        string $orderInfo,
        string $returnUrl,
        string $clientIp,
        array $extraParams = []
    ): string {
        $this->ensureConfigured();

        $paymentTime = now('Asia/Ho_Chi_Minh');

        $params = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $this->tmnCode,
            'vnp_Amount' => (int) round($amount * 100),
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => $paymentTime->format('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $clientIp,
            'vnp_Locale' => 'vn',
            'vnp_OrderInfo' => $orderInfo,
            'vnp_OrderType' => 'other',
            'vnp_ReturnUrl' => $returnUrl,
            'vnp_TxnRef' => $txnRef,
            'vnp_ExpireDate' => $paymentTime->copy()->addMinutes(15)->format('YmdHis'),
        ];

        $params = array_filter(
            array_merge($params, $extraParams),
            static fn ($value) => $value !== null && $value !== ''
        );

        ksort($params);

        $hashData = http_build_query($params);
        $secureHash = hash_hmac('sha512', $hashData, $this->hashSecret);

        return "{$this->vnpUrl}?{$hashData}&vnp_SecureHash={$secureHash}";
    }

    public function verifyCallback(Request $request): bool
    {
        if ($this->hashSecret === '') {
            return false;
        }

        $vnpSecureHash = $request->input('vnp_SecureHash');
        $params = $request->except(['vnp_SecureHash', 'vnp_SecureHashType']);

        ksort($params);

        $hashData = http_build_query($params);
        $expectedHash = hash_hmac('sha512', $hashData, $this->hashSecret);

        return hash_equals($expectedHash, $vnpSecureHash ?? '');
    }

    public function isPaymentSuccess(Request $request): bool
    {
        return $this->verifyCallback($request)
            && $request->input('vnp_ResponseCode') === '00';
    }

    public function getTransactionInfo(Request $request): array
    {
        return [
            'txn_ref' => $request->input('vnp_TxnRef'),
            'transaction_no' => $request->input('vnp_TransactionNo'),
            'amount' => (int) $request->input('vnp_Amount') / 100,
            'bank_code' => $request->input('vnp_BankCode'),
            'pay_date' => $request->input('vnp_PayDate'),
            'response_code' => $request->input('vnp_ResponseCode'),
        ];
    }

    private function ensureConfigured(): void
    {
        if ($this->tmnCode === '' || $this->hashSecret === '') {
            throw new RuntimeException('VNPay is not configured. Please set VNPAY_TMN_CODE and VNPAY_HASH_SECRET.');
        }
    }
}
