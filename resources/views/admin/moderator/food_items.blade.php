@extends('admin.moderator.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5>Quản lý Combo & Đồ ăn</h5>
        @if(isset($theater))
            <small class="text-muted">Rạp: <strong>{{ $theater['name'] ?? '' }}</strong></small>
        @endif
    </div>
    <a href="{{ route('moderator.foodItems.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm mới
    </a>
</div>

<div class="stat-card mb-3">
    <form method="GET" class="row g-2">
        <input type="hidden" name="route" value="moderator/foodItems">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên, mô tả..." value="{{ $search ?? '' }}">
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
            <button type="submit" class="btn btn-secondary w-100"><i class="fas fa-search"></i> Tìm</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('moderator.foodItems') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-redo"></i> Xóa lọc</a>
        </div>
    </form>
</div>

<div class="stat-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr><th>ID</th><th>Ảnh</th><th>Tên</th><th>Mô tả</th><th>Loại</th><th>Giá</th><th>Trạng thái</th><th>Thao tác</th></tr>
            </thead>
            <tbody>
                @if(empty($foodItems))
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            Chưa có combo/đồ ăn nào. <a href="{{ route('moderator.foodItems.create') }}" class="text-primary">Thêm mới ngay</a>
                        </td>
                    </tr>
                @else
                    @foreach($foodItems as $item)
                    <tr>
                        <td>{{ $item['id'] }}</td>
                        <td>
                            @if(!empty($item['image']))
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                            @else
                                <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['description'] ?? '' }}</td>
                        <td>
                            <span class="badge bg-{{ $item['type'] === 'combo' ? 'primary' : ($item['type'] === 'snack' ? 'warning' : 'info') }}">
                                {{ $item['type'] === 'combo' ? 'Combo' : ($item['type'] === 'snack' ? 'Snack' : 'Đồ uống') }}
                            </span>
                        </td>
                        <td>{{ number_format($item['price']) }}₫</td>
                        <td>
                            <span class="badge bg-{{ $item['is_active'] ? 'success' : 'secondary' }}">
                                {{ $item['is_active'] ? 'Hoạt động' : 'Ngừng bán' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('moderator.foodItems.edit', $item['id']) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <form action="{{ route('moderator.foodItems.destroy', $item['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa combo/đồ ăn này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
