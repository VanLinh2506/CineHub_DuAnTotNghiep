<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Showtime;
use Illuminate\Http\Request;

class VNPayService
{
    protected string $vnpUrl;
    protected string $tmnCode;
    protected string $hashSecret;
    protected string $returnUrl;

    public function __construct()
    {
        $this->vnpUrl     = config('services.vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $this->tmnCode    = config('services.vnpay.tmn_code');
        $this->hashSecret = config('services.vnpay.hash_secret');
        $this->returnUrl  = route('booking.vnpay-return');
    }

    /**
     * Tạo URL thanh toán VNPay
     */
    public function createPaymentUrl(Booking $booking, string $orderInfo, string $clientIp): string
    {
        $params = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $this->tmnCode,
            'vnp_Amount'     => (int) ($booking->total_amount * 100),
            'vnp_Command'    => 'pay',
            'vnp_CreateDate' => now()->format('YmdHis'),
            'vnp_CurrCode'   => 'VND',
            'vnp_IpAddr'     => $clientIp,
            'vnp_Locale'     => 'vn',
            'vnp_OrderInfo'  => $orderInfo,
            'vnp_OrderType'  => 'other',
            'vnp_ReturnUrl'  => $this->returnUrl,
            'vnp_TxnRef'     => $booking->vnp_txn_ref,
            'vnp_ExpireDate' => now()->addMinutes(15)->format('YmdHis'),
        ];

        ksort($params);

        $hashData = '';
        $query    = '';
        $i = 0;

        foreach ($params as $key => $value) {
            $encodedKey   = urlencode($key);
            $encodedValue = urlencode($value);

            if ($i++ > 0) {
                $hashData .= '&';
                $query    .= '&';
            }

            $hashData .= "{$encodedKey}={$encodedValue}";
            $query    .= "{$encodedKey}={$encodedValue}";
        }

        $secureHash = hash_hmac('sha512', $hashData, $this->hashSecret);

        return "{$this->vnpUrl}?{$query}&vnp_SecureHash={$secureHash}";
    }

    /**
     * Xác thực chữ ký từ VNPay callback
     */
    public function verifyCallback(Request $request): bool
    {
        $vnpSecureHash = $request->input('vnp_SecureHash');

        $params = $request->except(['vnp_SecureHash', 'vnp_SecureHashType']);
        ksort($params);

        $hashData = '';
        $i = 0;

        foreach ($params as $key => $value) {
            if ($value !== '' && $value !== null) {
                if ($i++ > 0) {
                    $hashData .= '&';
                }
                $hashData .= urlencode($key) . '=' . urlencode($value);
            }
        }

        $expectedHash = hash_hmac('sha512', $hashData, $this->hashSecret);

        return hash_equals($expectedHash, $vnpSecureHash ?? '');
    }

    /**
     * Kiểm tra thanh toán thành công
     */
    public function isPaymentSuccess(Request $request): bool
    {
        return $this->verifyCallback($request)
            && $request->input('vnp_ResponseCode') === '00';
    }

    /**
     * Lấy thông tin giao dịch từ callback
     */
    public function getTransactionInfo(Request $request): array
    {
        return [
            'txn_ref'       => $request->input('vnp_TxnRef'),
            'transaction_no'=> $request->input('vnp_TransactionNo'),
            'amount'        => (int) $request->input('vnp_Amount') / 100,
            'bank_code'     => $request->input('vnp_BankCode'),
            'pay_date'      => $request->input('vnp_PayDate'),
            'response_code' => $request->input('vnp_ResponseCode'),
        ];
    }
}
