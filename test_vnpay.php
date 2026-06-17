<?php
// Test VNPay URL generation
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\VNPayService;

$vnpay = new VNPayService();

$testUrl = $vnpay->createPaymentUrl(
    txnRef: 'TEST_' . time(),
    amount: 180000,
    orderInfo: 'Test thanh toan ve xem phim',
    returnUrl: 'http://127.0.0.1:8000/payment/vnpay/callback',
    clientIp: '127.0.0.1'
);

echo "VNPay URL được tạo:\n\n";
echo $testUrl . "\n\n";
echo "Bạn có thể copy và paste vào browser để test!\n";
