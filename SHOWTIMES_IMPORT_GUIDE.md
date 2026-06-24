# Hướng Dẫn Import Lịch Chiếu Phim

## Tổng Quan
Hệ thống cung cấp các Artisan commands để quản lý lịch chiếu phim tự động.

## Commands Có Sẵn

### 1. Import Lịch Chiếu Dày Đặc
```bash
php artisan import:showtimes
```

**Chức năng:**
- Import lịch chiếu cho 7 ngày tiếp theo (mặc định)
- Tạo lịch chiếu cho TẤT CẢ các phim có status "Chiếu rạp"
- Tạo lịch chiếu cho TẤT CẢ các rạp đang hoạt động (is_active = 1)
- Phân bổ ngẫu nhiên các suất chiếu cho mỗi phòng chiếu

**Tùy chọn:**
```bash
# Import cho 14 ngày
php artisan import:showtimes --days=14

# Import cho 30 ngày
php artisan import:showtimes --days=30
```

**Chi tiết:**
- Mỗi phòng chiếu có 3-5 phim khác nhau mỗi ngày (ngày thường)
- Mỗi phòng chiếu có 4-6 phim khác nhau mỗi ngày (cuối tuần)
- Mỗi phim có 2-4 suất chiếu mỗi ngày (ngày thường)
- Mỗi phim có 3-5 suất chiếu mỗi ngày (cuối tuần)
- Khung giờ chiếu: 08:00 - 23:30 (mỗi 30 phút)
- Giá vé tự động theo khung giờ:
  - Sáng (8h-12h): 45,000 - 70,000 VND
  - Chiều (12h-18h): 60,000 - 90,000 VND
  - Tối (18h-24h): 80,000 - 120,000 VND

### 2. Xem Thống Kê Lịch Chiếu
```bash
php artisan showtimes:stats
```

**Hiển thị:**
- Tổng số suất chiếu
- Phân bố theo ngày
- Phân bố theo rạp
- Thống kê giá vé (min, max, trung bình)

**Tùy chọn:**
```bash
# Xem thống kê 14 ngày
php artisan showtimes:stats --days=14
```

### 3. Chuẩn Bị Phim Cho Lịch Chiếu
```bash
php artisan movies:prepare-showtime
```

**Chức năng:**
- Hiển thị phân bố status của phim
- Cập nhật status của phim thành "showing" nếu cần

**Tùy chọn:**
```bash
# Chuyển TẤT CẢ phim sang status "showing"
php artisan movies:prepare-showtime --all
```

### 4. Xóa Lịch Chiếu
```bash
# Xóa TẤT CẢ lịch chiếu
php artisan showtimes:clear --all

# Chỉ xóa lịch chiếu tương lai
php artisan showtimes:clear --future
```

## Quy Trình Import Mới

### Lần Đầu Import
```bash
# Bước 1: Kiểm tra phim
php artisan movies:prepare-showtime

# Bước 2: Import lịch chiếu
php artisan import:showtimes

# Bước 3: Xem thống kê
php artisan showtimes:stats
```

### Import Lại Từ Đầu
```bash
# Bước 1: Xóa lịch chiếu cũ
php artisan showtimes:clear --all

# Bước 2: Import mới
php artisan import:showtimes

# Bước 3: Kiểm tra kết quả
php artisan showtimes:stats
```

### Thêm Lịch Chiếu Mới
```bash
# Import thêm 7 ngày tiếp theo
php artisan import:showtimes
```

## Kết Quả Import Mẫu

### Thống Kê Sau Khi Import
```
Total showtimes: 4,083

Showtimes by date:
- 2026-06-23: 501 showtimes
- 2026-06-24: 509 showtimes
- 2026-06-25: 496 showtimes
- 2026-06-26: 466 showtimes
- 2026-06-27: 808 showtimes (cuối tuần)
- 2026-06-28: 792 showtimes (cuối tuần)
- 2026-06-29: 511 showtimes

Showtimes by theater:
- Lotte Cinema: 894 showtimes
- CGV Vincom Center: 793 showtimes
- CGV Landmark: 710 showtimes
- BHD Star Cineplex: 630 showtimes
- Lotte Cinema Thanh Hóa: 507 showtimes
- Beta Cinema Thanh Hóa: 445 showtimes
- Galaxy Cinema: 104 showtimes

Price statistics:
- Min: 45,008 VND
- Max: 120,000 VND
- Avg: 79,891 VND
```

## Lưu Ý Quan Trọng

### Yêu Cầu Dữ Liệu
1. **Phim:** Phải có ít nhất 1 phim với status = "Chiếu rạp"
2. **Rạp:** Phải có ít nhất 1 rạp với is_active = 1
3. **Phòng chiếu:** Mỗi rạp phải có ít nhất 1 phòng chiếu (screens)

### Database Schema
```sql
-- Bảng showtimes yêu cầu các cột:
- id (primary key)
- movie_id (foreign key -> movies)
- theater_id (foreign key -> theaters)
- screen_id (foreign key -> theater_screens)
- show_date (date)
- show_time (time)
- price (decimal)
- created_at (timestamp)
```

### Tránh Trùng Lặp
Command tự động kiểm tra và KHÔNG tạo suất chiếu trùng lặp với:
- Cùng movie_id
- Cùng theater_id
- Cùng screen_id
- Cùng show_date
- Cùng show_time

## Troubleshooting

### Lỗi: "No movies with status 'Chiếu rạp' found"
**Giải pháp:**
```bash
php artisan movies:prepare-showtime --all
```

### Lỗi: "No active theaters found"
**Kiểm tra:**
```sql
SELECT * FROM theaters WHERE is_active = 1;
```

### Lỗi: Database constraint
**Kiểm tra:**
1. Foreign keys tồn tại
2. Movies, theaters, screens có dữ liệu
3. Các ID tham chiếu đúng

## Tips & Tricks

### Import Nhanh Cho Testing
```bash
# Import chỉ 3 ngày
php artisan import:showtimes --days=3
```

### Import Lịch Chiếu Dài Hạn
```bash
# Import 30 ngày (1 tháng)
php artisan import:showtimes --days=30

# Import 90 ngày (3 tháng)
php artisan import:showtimes --days=90
```

### Maintenance Hàng Ngày
Nên chạy command này mỗi ngày để đảm bảo luôn có lịch chiếu 7 ngày tới:
```bash
# Cron job (Linux/Mac)
0 0 * * * cd /path/to/project && php artisan import:showtimes

# Task Scheduler (Windows)
# Run daily at midnight: php artisan import:showtimes
```

## Contact
Nếu có vấn đề, vui lòng kiểm tra:
1. Database connection
2. Model relationships
3. Migration files
4. Log files: storage/logs/laravel.log
