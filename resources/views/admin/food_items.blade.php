@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Quản lý Combo & Đồ ăn</h2>
        <a href="{{ route('admin.foodItems.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm mới
        </a>
    </div>

    <!-- Filters -->
    <div class="stat-card mb-3">
        <form method="GET" action="{{ route('admin.foodItems.index') }}" class="row g-2">
            <input type="hidden" name="route" value="admin/foodItems">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên, mô tả..."
                       value="{{ htmlspecialchars($search ?? '') }}">
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả loại</option>
                    <option value="combo" {{ (isset($type) && $type === 'combo') ? 'selected' : '' }}>Combo</option>
                    <option value="snack" {{ (isset($type) && $type === 'snack') ? 'selected' : '' }}>Snack</option>
                    <option value="drink" {{ (isset($type) && $type === 'drink') ? 'selected' : '' }}>Đồ uống</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-search"></i> Tìm
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.foodItems.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-redo"></i> Xóa lọc
                </a>
            </div>
        </form>
    </div>

    <!-- Food Items Table -->
    <div class="stat-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Tên</th>
                        <th>Mô tả</th>
                        <th>Loại</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @if (empty($foodItems))
                        <tr>
                            <td colspan="8" class="text-center text-muted">Chưa có combo/đồ ăn nào</td>
                        </tr>
                    @else
                        @foreach ($foodItems as $item)
                            <tr>
                                <td>{{ $item['id'] }}</td>
                                <td>
                                    @if ($item['image'] ?? null)
                                        <img src="{{ $item['image'] }}"
                                             alt="{{ $item['name'] }}"
                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                    @else
                                        <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['description'] ?? '' }}</td>
                                <td>
                                    <span class="badge bg-{{
                                        $item['type'] === 'combo' ? 'primary' :
                                        ($item['type'] === 'snack' ? 'warning' : 'info')
                                    }}">
                                        {{ $item['type'] === 'combo' ? 'Combo' : ($item['type'] === 'snack' ? 'Snack' : 'Đồ uống') }}
                                    </span>
                                </td>
                                <td>{{ number_format($item['price'] ?? 0) }}₫</td>
                                <td>
                                    <span class="badge bg-{{ ($item['is_available'] ?? true) ? 'success' : 'danger' }}">
                                        {{ ($item['is_available'] ?? true) ? 'Có sẵn' : 'Tạm hết' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.foodItems.edit', $item['id']) }}" class="btn btn-outline-primary" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.foodItems.destroy', $item['id']) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn xóa &quot;{{ $item['name'] }}&quot;?');" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
