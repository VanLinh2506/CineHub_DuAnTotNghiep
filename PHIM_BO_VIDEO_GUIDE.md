# Hướng Dẫn Quản Lý Video Phim Bộ

## Cấu trúc thư mục video

Videos phim bộ được lưu trong:
```
storage/app/public/data/phim/phimbo/{folder-name}/
  ├── tap_1.mp4
  ├── tap_2.mp4
  ├── tap_3.mp4
  └── ...
```

Và được truy cập qua URL:
```
http://127.0.0.1:8000/storage/data/phim/phimbo/{folder-name}/tap_1.mp4
```

## Cách đặt tên folder

Folder phim nên được đặt tên theo chuẩn slug (không dấu, không khoảng trắng):
- "Phàm Nhân Tu Tiên" → `phamnhantutien`
- "Về Nhà Đi Con" → `venhadicon`
- "Game of Thrones" → `gameofthrones`

## Cách đặt tên file video

**QUAN TRỌNG:** Video phải có tên theo định dạng `tap_X.mp4` (không phải `tapX.mp4`)

Ví dụ:
- ✅ `tap_1.mp4`
- ✅ `tap_2.mp4`
- ✅ `tap_10.mp4`
- ❌ `tap1.mp4` (thiếu dấu gạch dưới)
- ❌ `episode_1.mp4` (không đúng định dạng)

## Commands quản lý episodes

### 1. Xem danh sách folders và phim bộ

```bash
php artisan episodes:sync-videos --list
```

Lệnh này sẽ hiển thị:
- Tất cả folders trong thư mục video
- Tất cả phim bộ trong database
- Số lượng video trong mỗi folder
- Số lượng tập trong database

### 2. Sync tất cả phim bộ

```bash
php artisan episodes:sync-videos
```

Lệnh này sẽ:
- Quét tất cả folders trong `storage/app/public/data/phim/phimbo/`
- Tự động tạo/cập nhật episodes trong database
- Cập nhật video_url cho mỗi episode

### 3. Sync một phim cụ thể

```bash
php artisan episodes:sync-videos {movie_id}
```

Ví dụ:
```bash
php artisan episodes:sync-videos 51
```

### 4. Sync với folder tùy chỉnh

Nếu tên folder không khớp với slug tự động:

```bash
php artisan episodes:sync-videos {movie_id} --folder=ten_folder_thuc_te
```

Ví dụ:
```bash
php artisan episodes:sync-videos 51 --folder=phamnhantutien
```

## Quy trình thêm phim bộ mới

### Bước 1: Upload videos
1. Tạo folder trong `storage/app/public/data/phim/phimbo/`
2. Đặt tên folder theo slug (không dấu, không space)
3. Upload các file video với tên `tap_1.mp4`, `tap_2.mp4`, ...

### Bước 2: Sync episodes
```bash
php artisan episodes:sync-videos {movie_id}
```

### Bước 3: Xóa cache
```bash
php artisan view:clear
```

### Bước 4: Kiểm tra
1. Truy cập trang xem phim: `http://127.0.0.1:8000/?route=movie/watch&id={movie_id}`
2. Kiểm tra danh sách tập hiển thị đúng
3. Click vào từng tập để test video play

## Troubleshooting

### Video không phát được

**Kiểm tra 1:** File có tồn tại không?
```bash
# Kiểm tra trong thư mục
dir storage\app\public\data\phim\phimbo\{folder-name}
```

**Kiểm tra 2:** URL có đúng không?
```bash
php test_video_url.php
```

**Kiểm tra 3:** Symlink storage có tồn tại không?
```bash
# Nếu không có, tạo lại:
php artisan storage:link
```

**Kiểm tra 4:** Episode có video_url đúng không?
```bash
php artisan episodes:sync-videos --list
```

### Folder không được tìm thấy

Nếu command báo "Không tìm thấy folder", có thể:
1. Folder name không khớp với slug tự động → Dùng `--folder=ten_folder_thuc_te`
2. Folder đang ở sai vị trí → Di chuyển vào `storage/app/public/data/phim/phimbo/`
3. Typo trong tên folder → Đổi tên cho đúng

### Episode không tự động tạo

Kiểm tra:
1. File video có đúng định dạng `tap_X.mp4` không?
2. Có quyền ghi vào database không?
3. Chạy lại command sync với option verbose

## Lưu ý quan trọng

1. **Luôn đặt tên file theo format `tap_X.mp4`** (có dấu gạch dưới)
2. **Không xóa video_url từ database thủ công** - Để command sync tự động quản lý
3. **Chạy sync sau khi upload video mới**
4. **Clear cache sau khi sync**: `php artisan view:clear`
5. **Videos được lưu trong storage, không phải public** - Đảm bảo symlink tồn tại

## Ví dụ hoàn chỉnh

```bash
# 1. Upload videos vào folder
# storage/app/public/data/phim/phimbo/phamnhantutien/tap_1.mp4
# storage/app/public/data/phim/phimbo/phamnhantutien/tap_2.mp4

# 2. Kiểm tra folder có videos
php artisan episodes:sync-videos --list

# 3. Sync episodes cho phim ID 51
php artisan episodes:sync-videos 51 --folder=phamnhantutien

# 4. Clear cache
php artisan view:clear

# 5. Test trong browser
# http://127.0.0.1:8000/?route=movie/watch&id=51
```

## Cấu trúc URL cuối cùng

- **Episode 1:** `http://127.0.0.1:8000/storage/data/phim/phimbo/phamnhantutien/tap_1.mp4`
- **Episode 2:** `http://127.0.0.1:8000/storage/data/phim/phimbo/phamnhantutien/tap_2.mp4`
- **Episode N:** `http://127.0.0.1:8000/storage/data/phim/phimbo/phamnhantutien/tap_N.mp4`
