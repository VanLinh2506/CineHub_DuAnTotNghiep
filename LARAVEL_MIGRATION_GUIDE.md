# Hướng dẫn chuyển đổi từ PHP thuần sang Laravel

## ✅ Đã hoàn thành

### Models Laravel Eloquent (trong `app/Models/`)
- ✅ User (đã có + updated relationships)
- ✅ Category
- ✅ Movie
- ✅ Episode  
- ✅ Review
- ✅ Comment
- ✅ CommentLike
- ✅ WatchHistory
- ✅ Transaction
- ✅ BookingPending
- ✅ Ticket
- ✅ Theater
- ✅ Screen
- ✅ Showtime
- ✅ Subscription
- ✅ Role
- ✅ UserToken
- ✅ FoodItem

### Controllers đã di chuyển (trong `app/Http/Controllers/`)
- ✅ HomeController (đã chuyển đổi sử dụng Eloquent)
- ✅ AuthController
- ✅ MovieController
- ✅ BookingController
- ✅ ProfileController
- ✅ ReviewController
- ✅ AdminController
- ✅ ModeratorController
- ✅ CounterStaffController
- ✅ NotificationController

## 📋 Bước tiếp theo

### 1. Cập nhật Controllers để dùng Eloquent

Các file trong `app/models/` (PHP thuần) vẫn còn nguyên để tham khảo.
Bây giờ cần cập nhật controllers để sử dụng Laravel Eloquent.

**Example - HomeController.php:**

Thay đổi từ:
```php
require_once __DIR__ . '/../models/MovieModel.php';
$movieModel = new MovieModel();
$movies = $movieModel->getAll();
```

Sang:
```php
use App\Models\Movie;
$movies = Movie::all();
```

### 2. Các thay đổi cần làm trong Controllers:

#### a) **Thay thế require_once bằng use statements**
```php
// Trước:
require_once __DIR__ . '/../models/MovieModel.php';

// Sau:
use App\Models\Movie;
```

#### b) **Thay thế khởi tạo model**
```php
// Trước:
$movieModel = new MovieModel();
$movie = $movieModel->getById($id);

// Sau:
$movie = Movie::find($id);
// hoặc
$movie = Movie::findOrFail($id); // throw 404 nếu không tìm thấy
```

#### c) **Thay thế các query phổ biến**

**Get all:**
```php
// Trước: $movies = $movieModel->getAll();
// Sau:
$movies = Movie::all();
```

**Get với relationships:**
```php
// Trước: Query thủ công với JOIN
// Sau:
$movies = Movie::with('category')->get();
```

**Search/Filter:**
```php
// Trước: Raw SQL với WHERE
// Sau:
$movies = Movie::where('status', 'Chiếu online')
    ->where('category_id', $categoryId)
    ->orderBy('rating', 'desc')
    ->get();
```

**Pagination:**
```php
// Trước: LIMIT OFFSET thủ công
// Sau:
$movies = Movie::paginate(12);
```

#### d) **Session và Redirect**
```php
// Trước:
$_SESSION['success'] = 'Thành công';
header('Location: ...');

// Sau:
return redirect()->route('home')->with('success', 'Thành công');
```

#### e) **Request Input**
```php
// Trước:
$title = $_POST['title'] ?? '';

// Sau (trong controller method):
public function store(Request $request) {
    $title = $request->input('title');
}
```

### 3. Cập nhật Routes (routes/web.php)

Cần thêm đầy đủ routes cho tất cả controllers.

### 4. Các Models còn trong app/models/ (PHP thuần)

Giữ lại để tham khảo logic, sau khi test kỹ có thể xóa.

## 🎯 Priority: Cập nhật các controller theo thứ tự

1. ✅ HomeController - Đã chuyển đổi
2. ⏳ AuthController - Đăng nhập/đăng ký (quan trọng)
3. ⏳ MovieController - Xem phim
4. ⏳ BookingController - Đặt vé
5. ⏳ ProfileController - Hồ sơ người dùng
6. ⏳ ReviewController - Đánh giá
7. ⏳ AdminController, ModeratorController, CounterStaffController - Admin features

## 📝 Notes quan trọng

- **Không xóa** các file PHP thuần trong `app/models/` cho đến khi đã test kỹ
- **Test từng controller** sau khi chuyển đổi
- **Database migrations** có thể cần tạo nếu chưa có
- **Validation** nên dùng Laravel Form Requests
- **Authentication** nên dùng Laravel Auth helpers thay vì session thủ công
