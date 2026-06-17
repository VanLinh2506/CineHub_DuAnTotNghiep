<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($food) ? 'Sửa' : 'Thêm' }} món</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-white min-h-screen p-6">
<div class="max-w-lg mx-auto">

    <h1 class="text-2xl font-bold text-yellow-400 mb-6">
        {{ isset($food) ? 'Sửa món' : 'Thêm món mới' }}
    </h1>

    <form method="POST"
          action="{{ isset($food) ? route('staff.food.update', $food) : route('staff.food.store') }}"
          enctype="multipart/form-data"
          class="space-y-5 bg-gray-900 p-6 rounded-xl">
        @csrf
        @if(isset($food)) @method('PUT') @endif

        <div>
            <label class="block text-sm text-gray-400 mb-1">Tên món *</label>
            <input type="text" name="name" required
                   value="{{ old('name', $food->name ?? '') }}"
                   class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">
            @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-400 mb-1">Loại *</label>
                <select name="type" required
                        class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">
                    <option value="food"  {{ old('type', $food->type ?? '') === 'food'  ? 'selected' : '' }}>🍿 Đồ ăn</option>
                    <option value="drink" {{ old('type', $food->type ?? '') === 'drink' ? 'selected' : '' }}>🥤 Đồ uống</option>
                    <option value="combo" {{ old('type', $food->type ?? '') === 'combo' ? 'selected' : '' }}>🎁 Combo</option>
                </select>
                @error('type') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Giá (VND) *</label>
                <input type="number" name="price" required min="1000" step="1000"
                       value="{{ old('price', $food->price ?? '') }}"
                       class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">
                @error('price') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm text-gray-400 mb-1">Mô tả</label>
            <textarea name="description" rows="3"
                      class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">{{ old('description', $food->description ?? '') }}</textarea>
        </div>

        <div>
            <label class="block text-sm text-gray-400 mb-1">Ảnh</label>
            @if(isset($food) && $food->image)
                <img src="{{ Storage::url($food->image) }}" class="h-24 rounded mb-2 object-cover">
            @endif
            <input type="file" name="image" accept="image/*"
                   class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">
            <p class="text-xs text-gray-500 mt-1">JPG, PNG, WebP — tối đa 2MB</p>
            @error('image') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3">
            <input type="checkbox" name="is_active" id="is_active" value="1"
                   {{ old('is_active', $food->is_active ?? true) ? 'checked' : '' }}
                   class="w-4 h-4 accent-yellow-400">
            <label for="is_active" class="text-sm text-gray-300">Đang bán</label>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-yellow-400 text-black font-semibold px-6 py-2 rounded-lg hover:bg-yellow-300">
                {{ isset($food) ? 'Cập nhật' : 'Thêm món' }}
            </button>
            <a href="{{ route('staff.food.index') }}"
               class="bg-gray-700 px-6 py-2 rounded-lg hover:bg-gray-600">Huỷ</a>
        </div>
    </form>

</div>
</body>
</html>
