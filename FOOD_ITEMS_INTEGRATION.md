# 🍿 Food Items Integration - Complete

## ✅ Status: HOÀN THIỆN

**Ngày:** June 13, 2026  
**Developer:** Kiro AI

---

## 📋 Overview

Tính năng **Combo & Đồ ăn** đã được tích hợp hoàn chỉnh vào luồng đặt vé. Người dùng có thể chọn thêm đồ ăn/nước uống sau khi xác nhận ghế ngồi.

---

## ✨ Tính năng

### 1. Food Modal (Pop-up)
- ✅ Tự động hiện ra sau khi xác nhận ghế (500ms delay)
- ✅ Hiển thị danh sách combo & đồ ăn
- ✅ Hình ảnh sản phẩm (hoặc icon mặc định)
- ✅ Tên, mô tả, giá của từng món
- ✅ Nút +/- để chọn số lượng (0-10)
- ✅ Hiển thị tổng tiền combo

### 2. Chọn số lượng
- ✅ Nút `-` giảm số lượng (minimum 0)
- ✅ Nút `+` tăng số lượng (maximum 10)
- ✅ Input số lượng readonly (chỉ thay đổi qua nút)
- ✅ Real-time update tổng tiền

### 3. Xác nhận
- ✅ Nút "Bỏ qua" - Đóng modal không chọn gì
- ✅ Nút "Xác nhận" - Lưu lựa chọn và update tổng tiền booking
- ✅ Hiển thị message thành công

### 4. Tích hợp với Booking
- ✅ Food items được thêm vào `food_items` field trong booking
- ✅ Tự động tính tổng tiền (vé + đồ ăn)
- ✅ Gửi kèm trong request booking
- ✅ Lưu vào database

---

## 🏗️ Architecture

### Frontend (Blade + JavaScript)

```
booking/index.blade.php
├── Food Modal HTML
│   ├── Modal overlay
│   ├── Modal header (title + close button)
│   ├── Modal body
│   │   ├── Food items list (@foreach)
│   │   │   ├── Image/Icon
│   │   │   ├── Name + Description + Price
│   │   │   └── Quantity controls (+/-)
│   │   └── Empty state (if no items)
│   └── Modal footer
│       ├── Total price display
│       └── Action buttons (Skip/Confirm)
│
└── JavaScript Functions
    ├── openFoodModal()              - Mở modal
    ├── closeFoodModal()             - Đóng modal
    ├── updateFoodQuantityModal()    - Cập nhật số lượng
    ├── syncFoodModalQuantities()    - Sync giữa modal và form
    ├── updateFoodModalTotal()       - Tính tổng tiền modal
    └── confirmFoodSelection()       - Xác nhận và lưu
```

### Backend (Laravel)

```
BookingController.php
└── processBooking()
    ├── Validate input
    ├── Get seats price
    ├── Get food items price (loop)
    │   ├── Find FoodItem by ID
    │   ├── Calculate: price * quantity
    │   └── Add to foodTotal
    ├── Calculate total: seats + food
    ├── Create booking (with food_items JSON)
    └── Redirect to VNPay
```

### Database

```
food_items table:
├── id
├── name
├── type (combo, drink, snack, etc.)
├── price
├── description
├── image
├── is_active
├── created_at
└── updated_at

booking_pending table:
├── ...
├── food_items (JSON)  ← Store {foodId: quantity}
└── ...
```

---

## 💻 Code Implementation

### JavaScript Functions Added

```javascript
// Open modal
function openFoodModal() {
    const modal = document.getElementById('foodModal');
    modal.style.display = 'block';
    syncFoodModalQuantities();
    updateFoodModalTotal();
}

// Close modal
function closeFoodModal() {
    const modal = document.getElementById('foodModal');
    modal.style.display = 'none';
}

// Update quantity
function updateFoodQuantityModal(foodId, change) {
    const input = document.getElementById('food_modal_' + foodId);
    let newValue = parseInt(input.value) + change;
    if (newValue < 0) newValue = 0;
    if (newValue > 10) newValue = 10;
    input.value = newValue;
    updateFoodModalTotal();
}

// Calculate modal total
function updateFoodModalTotal() {
    let total = 0;
    document.querySelectorAll('.food-modal-item').forEach(item => {
        const price = parseFloat(item.dataset.foodPrice);
        const qty = parseInt(document.getElementById('food_modal_' + item.dataset.foodId).value);
        total += price * qty;
    });
    document.getElementById('foodModalTotal').textContent = total.toLocaleString('vi-VN') + 'đ';
}

// Confirm selection
function confirmFoodSelection() {
    const foodData = {};
    document.querySelectorAll('.food-modal-item').forEach(item => {
        const foodId = item.dataset.foodId;
        const qty = parseInt(document.getElementById('food_modal_' + foodId).value);
        if (qty > 0) {
            foodData[foodId] = qty;
            // Create hidden input for form
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `food_items[${foodId}]`;
            input.value = qty;
            document.getElementById('bookingForm').appendChild(input);
        }
    });
    
    document.getElementById('foodItemsData').value = JSON.stringify(foodData);
    closeFoodModal();
    updateBookingSummary();
    showSuccess(`Đã thêm ${Object.keys(foodData).length} món vào đơn hàng!`);
}
```

### Auto-open Modal After Confirming Seats

```javascript
window.addEventListener('load', function() {
    const originalConfirmSeats = window.confirmSeats;
    
    window.confirmSeats = function() {
        const result = originalConfirmSeats();
        if (window.seatsConfirmed) {
            setTimeout(() => {
                openFoodModal();
            }, 500);
        }
        return result;
    };
});
```

### Controller Processing

```php
// In processBooking() method
$foodItems = $request->input('food_items', []);
$foodTotal = 0;
$filteredFoodItems = [];

foreach ($foodItems as $foodId => $quantity) {
    if ($quantity > 0) {
        $food = FoodItem::find($foodId);
        if ($food) {
            $foodTotal += $food->price * $quantity;
            $filteredFoodItems[$foodId] = $quantity;
        }
    }
}

$totalAmount += $foodTotal;

// Store in booking
$booking = Booking::create([
    // ...
    'food_items' => $filteredFoodItems, // JSON field
    'total_amount' => $totalAmount,
    // ...
]);
```

---

## 🎨 UI/UX

### Modal Design
- **Background:** Dark overlay with blur (backdrop-filter)
- **Content:** Gradient background (#1a1a2e → #16213e)
- **Border:** Yellow highlight on hover
- **Buttons:** Gradient yellow for confirm, transparent for skip
- **Icons:** Font Awesome icons
- **Animations:** Smooth transitions (0.3s)

### Food Item Card
- **Layout:** Horizontal (image + info + controls)
- **Image:** 70x70px, rounded corners
- **Text:** Name (bold), description (small), price (yellow)
- **Controls:** + and - buttons with quantity display
- **Hover:** Border color change to yellow

### Responsive
- **Modal:** 90% width, max 600px
- **Max height:** 80vh with scrollbar
- **Mobile friendly:** Touch-friendly buttons (36x36px minimum)

---

## 🧪 Testing

### Test Scenario 1: Basic Flow

```
1. Chọn phim, rạp, ngày, giờ chiếu ✅
2. Chọn ghế ✅
3. Click "Xác nhận ghế" ✅
4. Modal tự động hiện (500ms delay) ✅
5. Chọn combo (click +) ✅
6. Xem tổng tiền cập nhật ✅
7. Click "Xác nhận" ✅
8. Modal đóng ✅
9. Tổng tiền booking cập nhật ✅
10. Submit form → VNPay ✅
```

### Test Scenario 2: Skip Food

```
1-4. (Same as above)
5. Click "Bỏ qua" ✅
6. Modal đóng ✅
7. Không có food items trong booking ✅
8. Submit form → VNPay (chỉ tính tiền vé) ✅
```

### Test Scenario 3: Change Quantity

```
1-4. (Same as above)
5. Click + nhiều lần ✅
6. Click - để giảm ✅
7. Test minimum (0) ✅
8. Test maximum (10) ✅
9. Tổng tiền update đúng ✅
```

### Test Scenario 4: Multiple Items

```
1-4. (Same as above)
5. Chọn Combo 1: số lượng 2 ✅
6. Chọn Nước ngọt: số lượng 3 ✅
7. Chọn Bắp rang: số lượng 1 ✅
8. Tổng tiền = Σ(price × qty) ✅
9. Click "Xác nhận" ✅
10. Message: "Đã thêm 3 món vào đơn hàng!" ✅
```

### Edge Cases

```
✅ No food items in database → Show empty state
✅ Food item is_active = false → Not shown
✅ Food item image = null → Show default icon
✅ Click outside modal → Nothing happens (must click close/skip/confirm)
✅ Food items price = 0 → Still can select
✅ Food quantity = 0 → Not included in booking
```

---

## 📊 Database

### Food Items Sample Data

```sql
INSERT INTO food_items (name, type, price, description, is_active, created_at, updated_at) VALUES
('Combo 1 - Bắp + Nước', 'combo', 89000, '1 Bắp rang bơ size L + 2 Nước ngọt size L', 1, NOW(), NOW()),
('Combo 2 - Bắp + Nước + Khoai', 'combo', 129000, '1 Bắp size L + 2 Nước size L + 1 Khoai tây chiên', 1, NOW(), NOW()),
('Nước ngọt Pepsi', 'drink', 35000, 'Size L (700ml)', 1, NOW(), NOW()),
('Nước ngọt Coca', 'drink', 35000, 'Size L (700ml)', 1, NOW(), NOW()),
('Bắp rang bơ', 'snack', 45000, 'Size L', 1, NOW(), NOW()),
('Khoai tây chiên', 'snack', 40000, 'Size M', 1, NOW(), NOW()),
('Combo Family', 'combo', 249000, '2 Bắp size L + 4 Nước size L + 2 Khoai tây', 1, NOW(), NOW());
```

---

## 🔍 Debugging

### Check Food Items Loaded

```javascript
// Browser console
console.log(document.querySelectorAll('.food-modal-item').length);
```

### Check Food Data in Form

```javascript
// After confirm
console.log(document.getElementById('foodItemsData').value);
// Output: {"1":2,"3":1}
```

### Check Controller Receives Data

```php
// In BookingController
dd($request->input('food_items'));
// Output: ["1" => "2", "3" => "1"]
```

### Check Database

```sql
SELECT id, seats, food_items, total_amount 
FROM booking_pending 
WHERE user_id = ? 
ORDER BY created_at DESC 
LIMIT 1;
```

---

## ✅ Checklist

- [x] FoodItem model created
- [x] food_items table exists
- [x] Sample data inserted
- [x] Food modal HTML added
- [x] JavaScript functions added
  - [x] openFoodModal()
  - [x] closeFoodModal()
  - [x] updateFoodQuantityModal()
  - [x] updateFoodModalTotal()
  - [x] confirmFoodSelection()
  - [x] syncFoodModalQuantities()
- [x] Auto-open modal after confirm seats
- [x] Controller processes food_items
- [x] Food price added to total_amount
- [x] Food data stored in booking
- [x] UI/UX polished
- [x] Tested all scenarios

---

## 🚀 Next Steps (Optional)

### Enhancement Ideas

1. **Food Categories**
   - Tab để filter: Combo, Đồ ăn, Nước uống
   
2. **Food Images**
   - Upload hình ảnh thật cho từng món
   
3. **Popular Badge**
   - Hiển thị "Phổ biến" cho món bán chạy
   
4. **Combo Recommendations**
   - Suggest combo dựa trên số ghế
   
5. **Food Allergies**
   - Warning về thành phần dị ứng
   
6. **Nutritional Info**
   - Hiển thị calo, thành phần
   
7. **Food Promotion**
   - Giảm giá khi mua combo
   - Free item khi mua từ X ghế

---

## 📞 Support

**Issues?**
- Check browser console (F12)
- Check Laravel log: `storage/logs/laravel.log`
- Verify food_items table has data
- Verify is_active = 1 for food items

---

## 🎉 Conclusion

✅ **Food Items Integration COMPLETE!**

Users can now:
- Browse food items in modal
- Select quantities (0-10)
- See total price update
- Confirm or skip
- Food items included in booking
- Food price added to total payment

**Status:** Production Ready! 🚀

---

**Completed by:** Kiro AI  
**Date:** June 13, 2026  
**Version:** 1.0.0
