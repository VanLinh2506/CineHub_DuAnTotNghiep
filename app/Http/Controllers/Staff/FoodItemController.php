<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\FoodItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FoodItemController extends Controller
{
    /**
     * Danh sách combo & đồ ăn
     * GET /staff/food
     */
    public function index()
    {
        $items = FoodItem::orderBy('type')->orderBy('name')->paginate(20);

        return view('staff.food.index', compact('items'));
    }

    /**
     * Form tạo mới
     * GET /staff/food/create
     */
    public function create()
    {
        return view('staff.food.form');
    }

    /**
     * Lưu item mới
     * POST /staff/food
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:food,drink,combo',
            'price'       => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'   => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('food', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);

        FoodItem::create($data);

        return redirect()->route('staff.food.index')->with('success', 'Thêm món thành công.');
    }

    /**
     * Form chỉnh sửa
     * GET /staff/food/{id}/edit
     */
    public function edit(FoodItem $food)
    {
        return view('staff.food.form', compact('food'));
    }

    /**
     * Cập nhật
     * PUT /staff/food/{id}
     */
    public function update(Request $request, FoodItem $food)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:food,drink,combo',
            'price'       => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'   => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            // Xoá ảnh cũ
            if ($food->image) {
                Storage::disk('public')->delete($food->image);
            }
            $data['image'] = $request->file('image')->store('food', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');

        $food->update($data);

        return redirect()->route('staff.food.index')->with('success', 'Cập nhật thành công.');
    }

    /**
     * Xoá
     * DELETE /staff/food/{id}
     */
    public function destroy(FoodItem $food)
    {
        if ($food->image) {
            Storage::disk('public')->delete($food->image);
        }

        $food->delete();

        return back()->with('success', 'Đã xoá món.');
    }

    /**
     * Toggle trạng thái active/inactive (AJAX)
     * PATCH /staff/food/{id}/toggle
     */
    public function toggle(FoodItem $food)
    {
        $food->update(['is_active' => !$food->is_active]);
        $food->refresh();

        return response()->json([
            'success'   => true,
            'is_active' => $food->is_active,
        ]);
    }
}
