# 💳 VNPay Integration - CineHub

> **Status:** ✅ Production Ready  
> **Version:** 1.0.0  
> **Date:** June 13, 2026  
> **Developer:** Kiro AI

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Features](#features)
3. [Architecture](#architecture)
4. [Installation](#installation)
5. [Configuration](#configuration)
6. [Usage](#usage)
7. [Testing](#testing)
8. [Security](#security)
9. [Troubleshooting](#troubleshooting)
10. [Documentation](#documentation)

---

## 🎯 Overview

Tích hợp hoàn chỉnh **VNPay Payment Gateway** vào hệ thống CineHub với 2 chức năng chính:

1. **Đặt vé xem phim** - Thanh toán vé rạp qua VNPay
2. **Nạp điểm tài khoản** - Nạp điểm để sử dụng các tính năng premium

### Why VNPay?

- ✅ Cổng thanh toán phổ biến nhất Việt Nam
- ✅ Hỗ trợ đa dạng ngân hàng và ví điện tử
- ✅ Bảo mật cao với HMAC SHA512
- ✅ API đơn giản, dễ tích hợp
- ✅ Có sandbox để test miễn phí

---

## 🚀 Features

### 1. Booking Payment

- Thanh toán vé xem phim qua VNPay
- Tự động tạo vé với QR code
- Validation ghế phức tạp (12+ quy tắc)
- Hỗ trợ thêm đồ ăn vào booking
- Email confirmation (nếu configured)

### 2. Deposit Points

- Nạp điểm vào tài khoản
- 1 điểm = 1 VNĐ
- Minimum: 10,000 điểm
- Tự động cộng điểm sau thanh toán
- Lưu lịch sử giao dịch

### 3. Security Features

- ✅ **Signature Verification** - HMAC SHA512
- ✅ **Idempotent Processing** - Xử lý callback nhiều lần an toàn
- ✅ **Database Locking** - Tránh race condition
- ✅ **Amount Validation** - Kiểm tra số tiền khớp
- ✅ **Cache Expiration** - Timeout 20 phút
- ✅ **Transaction Deduplication** - Tránh tạo trùng

---

## 🏗️ Architecture

### Components

```
┌─────────────────────────────────────────────────────────┐
│                    Laravel Application                   │
│                                                          │
│  ┌───────────────────────────────────────────────────┐  │
│  │  Controllers                                      │  │
│  │  - BookingController (Booking payment)           │  │
│  │  - ProfileController (Deposit points)            │  │
│  └──────────────────┬────────────────────────────────┘  │
│                     │                                    │
│  ┌──────────────────▼───────────────────────────────┐  │
│  │  VNPayService                                    │  │
│  │  - createPaymentUrl()                            │  │
│  │  - verifyCallback()                              │  │
│  │  - isPaymentSuccess()                            │  │
│  └──────────────────┬────────────────────────────────┘  │
│                     │                                    │
└─────────────────────┼────────────────────────────────────┘
                      │
                      │ HTTPS
                      ▼
         ┌────────────────────────┐
         │    VNPay Gateway       │
         │  sandbox.vnpayment.vn  │
         └────────────────────────┘
```

### Database Tables

- **booking_pending** - Booking chờ thanh toán
- **tickets** - Vé đã thanh toán
- **transactions** - Lịch sử giao dịch
- **users** - Thông tin user (points)

### Cache Strategy

- **Deposit Pending** - 20 minutes
- **Deposit Processed** - 1 day
- **Booking Session** - 10 minutes

---

## 📦 Installation

### Prerequisites

- PHP >= 8.2
- Laravel >= 12.0
- SQLite/MySQL
- Composer
- VNPay Sandbox Account

### Step 1: Install Dependencies

```bash
# Already installed in composer.json
composer install
```

### Step 2: Publish Config

```bash
# Config already exists in config/services.php
php artisan config:clear
```

### Step 3: Run Migrations

```bash
# Migrations already exist
php artisan migrate
```

---

## ⚙️ Configuration

### 1. Register VNPay Sandbox Account

1. Visit: https://sandbox.vnpayment.vn/
2. Register account
3. Get credentials:
   - **TMN Code** (Terminal Code)
   - **Hash Secret** (Secret Key)

### 2. Update .env

```env
# VNPay Configuration
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_TMN_CODE=YOUR_TMN_CODE_HERE
VNPAY_HASH_SECRET=YOUR_HASH_SECRET_HERE
```

### 3. Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 4. Verify Routes

```bash
php artisan route:list --path=vnpay
```

Expected output:
```
GET|HEAD   payment/vnpay/callback
POST       payment/vnpay/create
POST       profile/deposit-vnpay
GET|HEAD   profile/vnpay-deposit-return
```

---

## 💻 Usage

### Booking Payment

```php
// User flow in browser:
// 1. Visit /booking
// 2. Select movie, theater, date, showtime
// 3. Select seats
// 4. Select food items (optional)
// 5. Click "Đặt vé"
// 6. System redirects to VNPay
// 7. User pays with test card
// 8. VNPay redirects back to /payment/vnpay/callback
// 9. System creates tickets
// 10. Redirect to /booking/history
```

### Deposit Points

```php
// User flow in browser:
// 1. Visit /profile
// 2. Enter points amount (min 10,000)
// 3. Click "Nạp điểm qua VNPay"
// 4. System redirects to VNPay
// 5. User pays with test card
// 6. VNPay redirects back to /profile/vnpay-deposit-return
// 7. System adds points to user
// 8. Redirect to /profile
```

### Programmatic Usage

```php
use App\Services\VNPayService;

// Create payment URL
$vnpay = app(VNPayService::class);
$url = $vnpay->createPaymentUrl(
    txnRef: 'BKG123',
    amount: 200000,
    orderInfo: 'Thanh toan ve phim',
    returnUrl: route('payment.vnpay.callback'),
    clientIp: $request->ip()
);

// Verify callback
if ($vnpay->verifyCallback($request)) {
    // Valid signature
}

// Check payment success
if ($vnpay->isPaymentSuccess($request)) {
    // Payment successful
}

// Get transaction info
$info = $vnpay->getTransactionInfo($request);
// ['txn_ref', 'transaction_no', 'amount', 'bank_code', ...]
```

---

## 🧪 Testing

### Test Credentials (Sandbox)

```
Card Number:  9704198526191432198
Card Holder:  NGUYEN VAN A
Issue Date:   07/15
OTP (Success): 123456
OTP (Fail):    999999
```

### Test Scenarios

#### 1. Success Case

```bash
# 1. Start server
php artisan serve

# 2. Visit booking page
http://127.0.0.1:8000/booking

# 3. Select movie, seats
# 4. Enter OTP: 123456
# 5. Check tickets created
```

#### 2. Failure Case

```bash
# Enter OTP: 999999
# Should redirect back with error
```

#### 3. Cancel Case

```bash
# Click "Hủy giao dịch" on VNPay
# Should redirect back with error
```

#### 4. Idempotent Test

```bash
# Copy callback URL from browser
# Paste URL again in new tab
# Should show "Already processed"
```

### Automated Testing

```bash
# Run PHPUnit tests (if exist)
php artisan test

# Check database
php artisan tinker
>>> App\Models\Booking::where('status', 'completed')->count()
>>> App\Models\Transaction::where('method', 'VNPay')->count()
```

---

## 🔒 Security

### 1. Signature Verification

Mọi callback từ VNPay đều được verify signature:

```php
$vnpSecureHash = $request->input('vnp_SecureHash');
$params = $request->except(['vnp_SecureHash', 'vnp_SecureHashType']);
ksort($params);
$hashData = http_build_query($params);
$expectedHash = hash_hmac('sha512', $hashData, $this->hashSecret);
return hash_equals($expectedHash, $vnpSecureHash ?? '');
```

### 2. Idempotent Processing

Xử lý callback nhiều lần mà không tạo duplicate:

```php
// Check already processed
if ($booking->status === 'completed') {
    return redirect()->route('booking.history')
        ->with('success', 'Already processed');
}

// Or use cache
if (Cache::has("vnpay:deposit:processed:{$txnRef}")) {
    return redirect()->route('profile.index')
        ->with('success', 'Already processed');
}
```

### 3. Database Locking

Tránh race condition:

```php
DB::transaction(function () use ($booking) {
    $lockedBooking = Booking::whereKey($booking->id)
        ->lockForUpdate()
        ->firstOrFail();
    
    // Process safely
});
```

### 4. Amount Validation

Kiểm tra số tiền:

```php
$callbackAmount = ((int) $request->input('vnp_Amount', 0)) / 100;
if ((float) $booking->total_amount !== (float) $callbackAmount) {
    return redirect()->back()
        ->with('error', 'Amount mismatch');
}
```

---

## 🐛 Troubleshooting

### Issue 1: "VNPay is not configured"

**Cause:** Missing VNPAY_TMN_CODE or VNPAY_HASH_SECRET

**Solution:**
```bash
# Check .env
type .env | findstr VNPAY

# Clear config
php artisan config:clear
```

### Issue 2: "Invalid signature"

**Cause:** Wrong VNPAY_HASH_SECRET

**Solution:**
```bash
# Double check hash secret
# Make sure no extra spaces
# Re-copy from VNPay dashboard
```

### Issue 3: "Transaction not found"

**Cause:** Cache expired or wrong txn_ref

**Solution:**
```bash
# Check cache
php artisan tinker
>>> Cache::get('vnpay:deposit:pending:DEP1_123')

# Try again from beginning
```

### Issue 4: "Seats already booked"

**Cause:** Race condition or test data

**Solution:**
```bash
# Check tickets
php artisan tinker
>>> Ticket::where('showtime_id', 1)->where('status', 'Đã đặt')->pluck('seat')

# Clear test data if needed
>>> Ticket::where('showtime_id', 1)->delete()
```

### Debugging

```bash
# Check logs
type storage\logs\laravel.log | findstr /i vnpay

# Check routes
php artisan route:list --path=vnpay

# Check database
php artisan tinker
>>> App\Models\Booking::latest()->first()
>>> App\Models\Transaction::latest()->first()
```

---

## 📚 Documentation

### Files

- **VNPAY_INTEGRATION_COMPLETE.md** - Tài liệu chi tiết đầy đủ
- **VNPAY_QUICK_TEST_GUIDE.md** - Hướng dẫn test nhanh
- **VNPAY_FLOW_DIAGRAM.txt** - Sơ đồ luồng xử lý
- **CHANGELOG_VNPAY.md** - Lịch sử thay đổi
- **VNPAY_SUMMARY.txt** - Tóm tắt nhanh
- **VNPAY_README.md** - File này

### Code Structure

```
app/
├── Services/
│   └── VNPayService.php          # Core VNPay service
├── Http/
│   └── Controllers/
│       ├── BookingController.php  # Booking payment
│       └── ProfileController.php  # Deposit points
└── Models/
    ├── Booking.php
    ├── Ticket.php
    ├── Transaction.php
    └── User.php

config/
└── services.php                   # VNPay config

routes/
└── web.php                        # VNPay routes

.env                               # Environment config
```

### API Reference

#### VNPayService

```php
// Create payment URL
public function createPaymentUrl(
    string $txnRef,
    float $amount,
    string $orderInfo,
    string $returnUrl,
    string $clientIp,
    array $extraParams = []
): string

// Create booking payment URL (wrapper)
public function createBookingPaymentUrl(
    Booking $booking,
    string $orderInfo,
    string $returnUrl,
    string $clientIp
): string

// Verify callback signature
public function verifyCallback(Request $request): bool

// Check payment success
public function isPaymentSuccess(Request $request): bool

// Get transaction info
public function getTransactionInfo(Request $request): array
```

---

## 🚀 Deployment

### Production Checklist

- [ ] Update VNPAY_TMN_CODE with production value
- [ ] Update VNPAY_HASH_SECRET with production value
- [ ] Change VNPAY_URL to production URL:
  ```env
  VNPAY_URL=https://vnpayment.vn/paymentv2/vpcpay.html
  ```
- [ ] Test on sandbox first
- [ ] Clear all caches
- [ ] Run migrations
- [ ] Monitor logs for errors
- [ ] Set up error alerts

### Production URL

```env
# Production (when ready)
VNPAY_URL=https://vnpayment.vn/paymentv2/vpcpay.html
VNPAY_TMN_CODE=YOUR_PRODUCTION_TMN_CODE
VNPAY_HASH_SECRET=YOUR_PRODUCTION_HASH_SECRET
```

---

## 📝 License

This integration is part of CineHub project.

---

## 🤝 Support

**Issues?** Check:
1. `storage/logs/laravel.log`
2. Browser console (F12)
3. Network tab (F12)
4. This documentation

**Still stuck?** Review:
- VNPAY_QUICK_TEST_GUIDE.md
- VNPAY_INTEGRATION_COMPLETE.md

---

## 🎉 Credits

**Developed by:** Kiro AI  
**Date:** June 13, 2026  
**Version:** 1.0.0

---

**🚀 Happy Coding!**
