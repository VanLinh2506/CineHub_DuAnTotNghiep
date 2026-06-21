@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="stat-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="fas fa-tags me-2"></i>Quản lý thể loại</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fas fa-plus me-1"></i> Thêm thể loại
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="60">ID</th>
                        <th>Tên thể loại</th>
                        <th>Thể loại cha</th>
                        <th width="120">Số phim</th>
                        <th width="150">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @if (empty($categories))
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                Chưa có thể loại nào
                            </td>
                        </tr>
                    @else
                        @foreach ($categories as $category)
                            <tr>
                                <td>{{ $category['id'] }}</td>
                                <td>
                                    <strong>{{ $category['name'] }}</strong>
                                </td>
                                <td>
                                    @if ($category['parent_id'])
                                        @php
                                            $parentName = '';
                                            foreach ($categories as $parent) {
                                                if ($parent['id'] == $category['parent_id']) {
                                                    $parentName = $parent['name'];
                                                    break;
                                                }
                                            }
                                        @endphp
                                        {{ $parentName }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $category['movie_count'] ?? 0 }} phim</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1" 
                                            onclick="editCategory(@json($category))"
                                            title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if (($category['movie_count'] ?? 0) == 0)
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteCategory({{ $category['id'] }}, '{{ $category['name'] }}')"
                                                title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-outline-secondary" disabled title="Không thể xóa - có phim đang sử dụng">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Thêm thể loại -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ url('?route=admin/categories/store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Thêm thể loại mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên thể loại <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Nhập tên thể loại">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Thể loại cha (tùy chọn)</label>
                        <select name="parent_id" class="form-select">
                            <option value="">-- Không có --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa thể loại -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ url('?route=admin/categories/update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Sửa thể loại</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên thể loại <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Thể loại cha (tùy chọn)</label>
                        <select name="parent_id" id="edit_parent_id" class="form-select">
                            <option value="">-- Không có --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Xóa thể loại -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ url('?route=admin/categories/delete') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="delete_id">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Xóa thể loại</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn chắc chắn muốn xóa thể loại <strong id="delete_name"></strong>?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Hành động này không thể hoàn tác.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i>Xóa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editCategory(category) {
        document.getElementById('edit_id').value = category.id;
        document.getElementById('edit_name').value = category.name;
        document.getElementById('edit_parent_id').value = category.parent_id || '';
        new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
    }

    function deleteCategory(id, name) {
        document.getElementById('delete_id').value = id;
        document.getElementById('delete_name').textContent = name;
        new bootstrap.Modal(document.getElementById('deleteCategoryModal')).show();
    }
</script>
@endsection
