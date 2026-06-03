@extends('layouts.app')

@php
    $title = 'Quản lý Combo & Đồ ăn';
@endphp

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5>{{ $title }}</h5>
        <a href="{{ url('?route=moderator/foodItemsCreate') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm mới
        </a>
    </div>

    <!-- Filters -->
    <div class="stat-card mb-3">
        <form method="GET" class="row g-2">
            <input type="hidden" name="route" value="moderator/foodItems">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên..." 
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
                        <th>Loại</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @if (empty($foodItems))
                        <tr>
                            <td colspan="7" class="text-center text-muted">Chưa có combo/đồ ăn nào</td>
                        </tr>
                    @else
                        @foreach ($foodItems as $item)
                            <tr>
                                <td>{{ $item['id'] }}</td>
                                <td>
                                    @if ($item['image'] ?? null)
                                        <img src="{{ $item['image'] }}" 
                                             alt="{{ $item['name'] }}" 
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    @else
                                        <i class="fas fa-image text-muted"></i>
                                    @endif
                                </td>
                                <td>{{ $item['name'] }}</td>
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
                                        <a href="{{ url('?route=moderator/foodItemsEdit&id=' . $item['id']) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteItem({{ $item['id'] }}, '{{ $item['name'] }}')" class="btn btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
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

<script>
    function deleteItem(id, name) {
        if (confirm('Bạn chắc chắn muốn xóa "' + name + '"?')) {
            window.location.href = '{{ url("?route=moderator/foodItemsDelete&id=") }}' + id;
        }
    }
</script>
@endsection
