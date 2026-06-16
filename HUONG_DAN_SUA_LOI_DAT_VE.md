# Hướng Dẫn Sửa Lỗi Đặt Vé - Không Hiển Thị Giờ Chiếu

## Vấn đề
Khi chọn ngày, không hiển thị giờ chiếu phim để đặt ghế.

## Nguyên nhân
1. Field name không khớp: Controller đang gọi `screen->name` nhưng trong database là `screen_name`
2. Thiếu dữ liệu showtime trong database
3. Route API có thể chưa hoạt động đúng

## Đã sửa

### 1. Sửa BookingController.php
**File:** `app/Http/Controllers/BookingController.php`

**Thay đổi trong method `getShowtimesByDate`:**
```php
// Trước (SAI):
'screen_name' => $showtime->screen->name ?? 'N/A',

// Sau (ĐÚNG):
'screen_name' => $showtime->screen->screen_name ?? 'N/A',
```

**Thay đổi trong method `getSeatMap`:**
```php
// Đảm bảo sử dụng screen_name
'name' => $showtime->screen->screen_name ?? '',
```

### 2. Thêm logging để debug
- Thêm Log::info() trong method `getShowtimesByDate` để tracking
- Có thể xem log tại: `storage/logs/laravel.log`

## Cách kiểm tra

### 1. Kiểm tra dữ liệu trong database
Chạy file: `test_showtimes.sql` trong phpMyAdmin để kiểm tra:
- Có showtime nào không?
- Showtime có liên kết với screen không?
- Showtime có ngày >= hôm nay không?

```sql
-- Kiểm tra nhanh
SELECT 
    s.*,
    m.title as movie_title,
    t.name as theater_name,
    sc.screen_name
FROM showtimes s
LEFT JOIN movies m ON s.movie_id = m.id
LEFT JOIN theaters t ON s.theater_id = t.id
LEFT JOIN theater_screens sc ON s.screen_id = sc.id
WHERE s.show_date >= CURDATE()
LIMIT 10;
```

### 2. Nếu chưa có dữ liệu
Chạy file: `insert_sample_showtimes.sql` để insert dữ liệu mẫu

### 3. Test trên browser

1. **Mở trình duyệt** và truy cập: http://localhost:8000
2. **Vào trang đặt vé:** Chọn một bộ phim
3. **Mở Developer Tools** (F12)
4. **Chọn tab Console** để xem log JavaScript
5. **Chọn tab Network** để xem API calls

**Các bước test:**
```
Bước 1: Chọn phim
  ✓ Hiển thị danh sách rạp

Bước 2: Chọn rạp
  ✓ Hiển thị danh sách ngày (7 ngày tới)
  
Bước 3: Chọn ngày
  ✓ Gọi API: /api/booking/showtimes?movie_id=X&theater_id=Y&date=Z
  ✓ Hiển thị danh sách giờ chiếu
  
Bước 4: Chọn giờ chiếu
  ✓ Gọi API: /api/booking/seatMap?showtime_id=X
  ✓ Hiển thị sơ đồ ghế
```

### 4. Kiểm tra API trực tiếp

**Test API getShowtimesByDate:**
```
http://localhost:8000/api/booking/showtimes?movie_id=1&theater_id=1&date=2026-06-09
```

**Kết quả mong đợi:**
```json
{
  "showtimes": [
    {
      "id": 1,
      "show_time": "10:00",
      "screen_name": "Phòng 1",
      "screen_type": "2D",
      "price": "90000.00"
    },
    ...
  ]
}
```

**Nếu trả về `{"showtimes": []}`:**
- Kiểm tra lại movie_id, theater_id có đúng không
- Kiểm tra có showtime cho ngày đó không (chạy test_showtimes.sql)
- Kiểm tra log tại `storage/logs/laravel.log`

### 5. Xem log Laravel

**Windows:**
```cmd
type storage\logs\laravel.log | findstr "GetShowtimesByDate"
```

**Hoặc mở file:**
```
storage/logs/laravel.log
```

Tìm các dòng log:
```
[2026-06-09 ...] local.INFO: GetShowtimesByDate called {"movie_id":"1","theater_id":"1","date":"2026-06-09"}
[2026-06-09 ...] local.INFO: Total showtimes for date (before time filter) {"count":4}
[2026-06-09 ...] local.INFO: Showtimes after time filter {"count":3}
[2026-06-09 ...] local.INFO: Returning showtimes {"count":3}
```

## Xử lý lỗi thường gặp

### Lỗi 1: "Không có suất chiếu nào cho ngày này"
**Nguyên nhân:** Không có dữ liệu showtime trong database
**Giải pháp:** Chạy `insert_sample_showtimes.sql`

### Lỗi 2: "screen_name" = null
**Nguyên nhân:** Showtime không có screen_id hoặc screen_id không tồn tại
**Giải pháp:** 
```sql
-- Kiểm tra
SELECT s.id, s.screen_id, sc.screen_name
FROM showtimes s
LEFT JOIN theater_screens sc ON s.screen_id = sc.id
WHERE sc.id IS NULL;

-- Sửa: Update screen_id cho các showtime
UPDATE showtimes s
JOIN theater_screens sc ON s.theater_id = sc.theater_id
SET s.screen_id = sc.id
WHERE s.screen_id IS NULL
LIMIT 1;
```

### Lỗi 3: Console log "Missing required data"
**Nguyên nhân:** selectedTheaterId, selectedDate, hoặc currentMovieId bị null
**Giải pháp:** Kiểm tra JavaScript trong view, đảm bảo:
```javascript
console.log('Current state:', {
    selectedTheaterId: selectedTheaterId,
    selectedDate: selectedDate,
    currentMovieId: currentMovieId
});
```

### Lỗi 4: Network error "Failed to fetch"
**Nguyên nhân:** Route không hoạt động hoặc server không chạy
**Giải pháp:** 
1. Kiểm tra server đang chạy: `php artisan serve`
2. Kiểm tra route: `php artisan route:list | findstr showtime`

## Checklist hoàn thành

- [x] Sửa field name từ `screen->name` thành `screen->screen_name`
- [x] Thêm logging để debug
- [x] Tạo file test SQL
- [x] Tạo file insert dữ liệu mẫu
- [ ] Kiểm tra dữ liệu trong database
- [ ] Test API trực tiếp
- [ ] Test trên browser
- [ ] Kiểm tra log Laravel

## Liên hệ support
Nếu vẫn gặp lỗi sau khi làm theo hướng dẫn, cung cấp:
1. Screenshot console log (F12)
2. Screenshot network tab (F12)
3. Nội dung file `storage/logs/laravel.log` (phần liên quan)
4. Kết quả query từ `test_showtimes.sql`
