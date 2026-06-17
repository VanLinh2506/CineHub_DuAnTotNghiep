# 🧪 VNPay Quick Test Guide

## 🚀 Khởi động dự án

```bash
# 1. Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 2. Chạy server
php artisan serve
# Server: http://127.0.0.1:8000
```

---

## 💳 Thông Tin Test VNPay Sandbox

### Card Test
```
Card Number:  9704 1985 2619 1432 198
Card Holder:  NGUYEN VAN A
Issue Date:   07/15
OTP:          123456
```

### Các Response Code Test

| Code | Mô tả | Cách test |
|------|-------|-----------|
| 00 | Thành công | Nhập đúng OTP: 123456 |
| 07 | Trừ tiền thành công, nghi vấn | Nhập OTP: 123457 |
| 09 | Thẻ chưa đăng ký SMS | Nhập OTP: 123458 |
| 10 | Xác thực không đúng > 3 lần | Nhập sai OTP 3 lần |
| 11 | Hết hạn chờ thanh toán | Chờ 15 phút |
| 12 | Thẻ bị khóa | Nhập OTP: 123459 |
| 13 | OTP không đúng | Nhập OTP: 999999 |
| 24 | Hủy giao dịch | Click "Hủy" trên VNPay |
| 51 | Không đủ tiền | Nhập OTP: 123451 |

---

## 🎬 Test 1: Đặt Vé Xem Phim

### Bước 1: Truy cập trang đặt vé
```
URL: http://127.0.0.1:8000/booking
```

### Bước 2: Chọn phim
- Click vào một phim bất kỳ
- Hoặc click nút "Đặt vé"

### Bước 3: Chọn rạp và ngày
- Chọn rạp trong danh sách
- Chọn ngày (trong 7 ngày tới)
- Chọn giờ chiếu

### Bước 4: Chọn ghế
**Test các trường hợp:**

✅ **Hợp lệ:**
- Chọn 1 ghế ngoài cùng: `A1`, `A10`
- Chọn 2 ghế liền nhau: `B3, B4`
- Chọn 3 ghế liền nhau: `C5, C6, C7`
- Chọn ghế đôi: `J1, J2` (couple seats)

❌ **Không hợp lệ:**
- Chọn 1 ghế ở giữa: `A5` (có >= 3 ghế trống 2 bên)
- Chọn ghế thứ 2 từ đầu: `A2` (khi A1 chưa đặt)
- Chọn 2 ghế cách nhau: `D3, D5`
- Chọn ghế bỏ trống ở giữa: `E3, E5, E6`

### Bước 5: Chọn đồ ăn (Optional)
- Chọn combo bắp nước
- Tăng/giảm số lượng

### Bước 6: Nhập email
```
Email: test@example.com
```

### Bước 7: Xác nhận đặt vé
- Click "Xác nhận đặt vé"
- Kiểm tra tổng tiền
- Click "Thanh toán"

### Bước 8: Thanh toán VNPay

**Redirect sang:** `https://sandbox.vnpayment.vn/...`

**Nhập thông tin:**
```
Card Number:  9704198526191432198
Card Holder:  NGUYEN VAN A
Issue Date:   07/15
```

**Click "Tiếp tục"**

**Nhập OTP:**
```
OTP: 123456  (thành công)
```

### Bước 9: Kiểm tra kết quả

**Redirect về:** `http://127.0.0.1:8000/booking/history`

**Kiểm tra:**
- [x] Hiển thị thông báo "Đặt vé thành công!"
- [x] Vé xuất hiện trong danh sách
- [x] Có QR code
- [x] Thông tin phim, rạp, giờ chiếu đúng
- [x] Ghế ngồi đúng

### Bước 10: Kiểm tra database

```bash
# SQLite
php artisan tinker
```

```php
// Kiểm tra booking
$booking = App\Models\Booking::latest()->first();
$booking->status; // 'completed'
$booking->total_amount;
$booking->vnp_txn_ref;

// Kiểm tra tickets
$tickets = App\Models\Ticket::where('booking_pending_id', $booking->id)->get();
$tickets->count(); // Số ghế đã chọn
$tickets->pluck('qr_code'); // QR codes

// Kiểm tra transaction
$transaction = App\Models\Transaction::where('related_id', $booking->id)
    ->where('type', 'ticket')
    ->first();
$transaction->status; // 'Thành công'
$transaction->method; // 'VNPay'
```

---

## 💰 Test 2: Nạp Điểm Tài Khoản

### Bước 1: Đăng nhập
```
URL: http://127.0.0.1:8000/login
```

### Bước 2: Vào trang Profile
```
URL: http://127.0.0.1:8000/profile
```

### Bước 3: Nạp điểm

**Tìm phần "Nạp điểm qua VNPay"**

**Nhập số điểm:**
```
Điểm: 50000
(Tối thiểu: 10,000)
```

**Click "Nạp điểm qua VNPay"**

### Bước 4: Thanh toán VNPay

**Nhập thông tin card (như test 1)**
```
OTP: 123456
```

### Bước 5: Kiểm tra kết quả

**Redirect về:** `http://127.0.0.1:8000/profile`

**Kiểm tra:**
- [x] Hiển thị "Nạp điểm thành công!"
- [x] Số dư điểm đã tăng: 50,000 điểm
- [x] Lịch sử giao dịch có record mới

### Bước 6: Kiểm tra database

```php
// Kiểm tra user points
$user = App\Models\User::find(Auth::id());
$user->points; // Đã tăng 50,000

// Kiểm tra transaction
$transaction = App\Models\Transaction::where('user_id', $user->id)
    ->where('type', 'deposit')
    ->latest()
    ->first();
    
$transaction->amount; // 50000
$transaction->method; // 'VNPay'
$transaction->status; // 'Thành công'
```

---

## 🧪 Test 3: Edge Cases

### Test 3.1: Thanh toán thất bại

**Nhập OTP:**
```
OTP: 999999  (sai OTP)
```

**Kết quả mong đợi:**
- Redirect về trang booking
- Hiển thị "Thanh toán thất bại"
- Booking status = 'cancelled'
- Không tạo tickets

### Test 3.2: Hủy giao dịch

**Click "Hủy giao dịch" trên VNPay**

**Kết quả mong đợi:**
- Redirect về trang booking
- Hiển thị "Thanh toán bị hủy"
- Booking status = 'cancelled'

### Test 3.3: Callback nhiều lần (Idempotent)

**Cách test:**
```bash
# Lưu URL callback từ VNPay
# VD: http://127.0.0.1:8000/payment/vnpay/callback?vnp_TxnRef=BKG1_1234567890&...

# Paste lại URL này trong browser (lần 2)
```

**Kết quả mong đợi:**
- Không tạo tickets trùng
- Hiển thị "Giao dịch đã được xác nhận trước đó"
- Redirect về booking history

### Test 3.4: Sửa amount trong callback

**Cách test:**
```bash
# Sửa vnp_Amount trong URL callback
# VD: vnp_Amount=10000000 → vnp_Amount=5000000
```

**Kết quả mong đợi:**
- Hiển thị "Số tiền thanh toán không khớp"
- Booking vẫn pending hoặc cancelled

### Test 3.5: Giả mạo signature

**Cách test:**
```bash
# Sửa vnp_SecureHash trong URL callback
# VD: vnp_SecureHash=abc123... → vnp_SecureHash=fake123...
```

**Kết quả mong đợi:**
- Hiển thị "Chữ ký không hợp lệ"
- Không xử lý callback

### Test 3.6: Timeout (Expired)

**Cách test:**
```bash
# Chờ > 20 phút sau khi tạo deposit
# Sau đó mới thanh toán VNPay
```

**Kết quả mong đợi:**
- Hiển thị "Giao dịch không hợp lệ hoặc đã hết hạn"
- Không cộng điểm

---

## 📊 Kiểm Tra Log

### Laravel Log
```bash
# Windows
type storage\logs\laravel.log | findstr /i "vnpay booking deposit"

# Hoặc mở file
notepad storage\logs\laravel.log
```

**Tìm các log quan trọng:**
```
[INFO] VNPay callback received {"txn_ref":"BKG1_1234567890"}
[INFO] Booking status: completed
[INFO] Created 3 tickets
[INFO] Transaction created successfully
```

### Browser Console
```javascript
// F12 > Console
console.log('Check for errors');
```

### Network Tab
```
F12 > Network > Filter: "vnpay"
```

**Kiểm tra:**
- POST `/booking/process` → Status 302 (redirect)
- GET `/payment/vnpay/callback` → Status 302 (redirect)
- Response headers

---

## ✅ Checklist Tổng Hợp

### Booking Flow
- [ ] Chọn phim, rạp, ngày, giờ ✅
- [ ] Chọn ghế hợp lệ ✅
- [ ] Chọn ghế không hợp lệ → Báo lỗi ✅
- [ ] Chọn đồ ăn ✅
- [ ] Tạo booking pending ✅
- [ ] Redirect VNPay ✅
- [ ] Thanh toán thành công ✅
- [ ] Tạo tickets với QR code ✅
- [ ] Tạo transaction ✅
- [ ] Hiển thị vé trong history ✅

### Deposit Flow
- [ ] Đăng nhập ✅
- [ ] Nhập số điểm (>= 10,000) ✅
- [ ] Tạo cache pending ✅
- [ ] Redirect VNPay ✅
- [ ] Thanh toán thành công ✅
- [ ] Cộng điểm cho user ✅
- [ ] Tạo transaction ✅
- [ ] Hiển thị số dư mới ✅

### Edge Cases
- [ ] Thanh toán thất bại ✅
- [ ] Hủy giao dịch ✅
- [ ] Callback nhiều lần (idempotent) ✅
- [ ] Sửa amount ✅
- [ ] Giả mạo signature ✅
- [ ] Timeout ✅

---

## 🐛 Debug Commands

```bash
# Clear everything
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Check routes
php artisan route:list | findstr vnpay

# Check config
php artisan tinker
>>> config('services.vnpay')

# Check cache
php artisan tinker
>>> Cache::get('vnpay:deposit:pending:DEP1_1234567890')

# Clear specific cache
>>> Cache::forget('vnpay:deposit:pending:DEP1_1234567890')

# Check database
>>> App\Models\Booking::where('status', 'pending')->count()
>>> App\Models\Booking::where('status', 'completed')->count()
>>> App\Models\Transaction::where('method', 'VNPay')->count()
```

---

## 📞 Troubleshooting

### Lỗi thường gặp

1. **"VNPay is not configured"**
   ```bash
   # Kiểm tra .env
   type .env | findstr VNPAY
   
   # Clear config
   php artisan config:clear
   ```

2. **"Không tìm thấy booking"**
   ```bash
   # Kiểm tra vnp_txn_ref
   php artisan tinker
   >>> App\Models\Booking::where('vnp_txn_ref', 'BKG1_1234567890')->first()
   ```

3. **"Chữ ký không hợp lệ"**
   ```bash
   # Kiểm tra VNPAY_HASH_SECRET
   # Đảm bảo không có khoảng trắng thừa
   ```

4. **"Ghế đã được đặt"**
   ```bash
   # Kiểm tra tickets
   php artisan tinker
   >>> App\Models\Ticket::where('showtime_id', 1)->where('status', 'Đã đặt')->pluck('seat')
   ```

---

## 🎯 Test Results Expected

### Success Case
```
✅ Booking completed successfully
✅ 3 tickets created with QR codes
✅ Transaction saved with status "Thành công"
✅ User balance updated (for deposit)
✅ Email sent (if configured)
✅ Redirect to success page
```

### Failure Case
```
❌ Payment failed or cancelled
❌ Booking status = 'cancelled'
❌ No tickets created
❌ No transaction created
❌ Redirect to booking page with error message
```

---

**Happy Testing! 🚀**
