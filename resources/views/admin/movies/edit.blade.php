@extends('layouts.admin')

@section('title','Sửa phim')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Sửa phim</h5>
    <a href="?route=admin/movies" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="stat-card">
    <form method="POST" action="{{ route('admin.movies.update') }}"
      enctype="multipart/form-data">

    @csrf
        <input type="hidden" name="id" value="{{ $movie['id'] }}">
        
        <div class="row">
            <div class="col-md-12 mb-3">
                <label for="title" class="form-label">Tiêu đề phim <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" value="{{ $movie['title'] }}" required>
            </div>
            
            <?php 
            // Lấy danh sách category_ids của phim (từ bảng movie_categories nếu có, hoặc từ category_id cũ)
            $selectedCategoryIds = [];
            if (!empty($movieCategories)) {
                $selectedCategoryIds = array_column($movieCategories, 'category_id');
            } elseif (!empty($movie['category_id'])) {
                $selectedCategoryIds = [$movie['category_id']];
            }
            ?>
            <div class="col-md-6 mb-3">
                <label for="category_ids" class="form-label">Thể loại <small class="text-muted">(có thể chọn nhiều)</small></label>
                <select class="form-select" id="category_ids" name="category_ids[]" multiple size="4">
                    @foreach($categories as $cat)
                        <option value="<?php echo $cat['id']; ?>" <?php echo in_array($cat['id'], $selectedCategoryIds) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">Giữ Ctrl (Windows) hoặc Cmd (Mac) để chọn nhiều thể loại</small>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="level" class="form-label">Cấp độ</label>
                <select class="form-select" id="level" name="level">
                    <option value="Free" <?php echo ($movie['level'] == 'Free') ? 'selected' : ''; ?>>Free</option>
                    <option value="Silver" <?php echo ($movie['level'] == 'Silver') ? 'selected' : ''; ?>>Silver</option>
                    <option value="Gold" <?php echo ($movie['level'] == 'Gold') ? 'selected' : ''; ?>>Gold</option>
                    <option value="Premium" <?php echo ($movie['level'] == 'Premium') ? 'selected' : ''; ?>>Premium</option>
                </select>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="status_admin" class="form-label">Trạng thái Admin</label>
                <select class="form-select" id="status_admin" name="status_admin">
                    <option value="draft" <?php echo (($movie['status_admin'] ?? 'draft') == 'draft') ? 'selected' : ''; ?>>Draft</option>
                    <option value="scheduled" <?php echo (($movie['status_admin'] ?? '') == 'scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                    <option value="published" <?php echo (($movie['status_admin'] ?? '') == 'published') ? 'selected' : ''; ?>>Published</option>
                    <option value="archived" <?php echo (($movie['status_admin'] ?? '') == 'archived') ? 'selected' : ''; ?>>Archived</option>
                </select>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="type" class="form-label">Loại phim <span class="text-danger">*</span></label>
                <select class="form-select" id="type" name="type" onchange="toggleSeriesSection(); toggleDurationField();">
                    <option value="phimle" <?php echo (($movie['type'] ?? 'phimle') == 'phimle') ? 'selected' : ''; ?>>Phim lẻ</option>
                    <option value="phimbo" <?php echo (($movie['type'] ?? '') == 'phimbo') ? 'selected' : ''; ?>>Phim bộ</option>
                </select>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status" onchange="toggleTheaterSection(); toggleDurationField();">
                    <option value="Sắp chiếu" <?php echo ($movie['status'] == 'Sắp chiếu') ? 'selected' : ''; ?>>Sắp chiếu</option>
                    <option value="Chiếu rạp" <?php echo ($movie['status'] == 'Chiếu rạp') ? 'selected' : ''; ?>>Chiếu rạp</option>
                    <option value="Chiếu online" <?php echo ($movie['status'] == 'Chiếu online') ? 'selected' : ''; ?>>Chiếu online</option>
                </select>
            </div>
            
            <!-- Thời lượng chỉ hiện cho phim lẻ chiếu rạp -->
            <div class="col-md-3 mb-3" id="durationSection" style="display: <?php echo (($movie['type'] ?? 'phimle') == 'phimle' && $movie['status'] == 'Chiếu rạp') ? 'block' : 'none'; ?>;">
                <label for="duration" class="form-label">Thời lượng (phút)</label>
                <input type="number" class="form-control" id="duration" name="duration" value="<?php echo $movie['duration'] ?? ''; ?>" min="0">
                <small class="text-muted">Chỉ áp dụng cho phim chiếu rạp</small>
            </div>
            
            <div class="col-md-3 mb-3">
                <!-- Placeholder để giữ layout -->
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="age_rating" class="form-label">Độ tuổi</label>
                <input type="text" class="form-control" id="age_rating" name="age_rating" value="<?php echo htmlspecialchars($movie['age_rating'] ?? ''); ?>" placeholder="VD: T18, P">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="director" class="form-label">Đạo diễn</label>
                <input type="text" class="form-control" id="director" name="director" value="<?php echo htmlspecialchars($movie['director'] ?? ''); ?>">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="actors" class="form-label">Diễn viên</label>
                <input type="text" class="form-control" id="actors" name="actors" value="<?php echo htmlspecialchars($movie['actors'] ?? ''); ?>" placeholder="VD: Diễn viên 1, Diễn viên 2">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="country" class="form-label">Quốc gia</label>
                <input type="text" class="form-control" id="country" name="country" value="<?php echo htmlspecialchars($movie['country'] ?? ''); ?>" placeholder="VD: Việt Nam, Mỹ">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="language" class="form-label">Ngôn ngữ</label>
                <input type="text" class="form-control" id="language" name="language" value="<?php echo htmlspecialchars($movie['language'] ?? ''); ?>" placeholder="VD: Tiếng Việt, Tiếng Anh">
            </div>
            
            <div class="col-md-12 mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($movie['description'] ?? ''); ?></textarea>
            </div>
            
            <!-- Upload Poster/Thumbnail -->
            <div class="col-md-6 mb-3">
                <label for="thumbnail_file" class="form-label">Poster/Thumbnail</label>
                <div class="upload-box" id="thumbnailUploadBox" onclick="document.getElementById('thumbnail_file').click()">
                    <input type="file" class="form-control d-none" id="thumbnail_file" name="thumbnail_file" accept="image/*" onchange="previewImage(this, 'thumbnailPreview', 'thumbnailUploadBox')">
                    @if(!empty($movie['thumbnail']))
                        <img id="thumbnailPreview" class="upload-preview" src="<?php echo htmlspecialchars($movie['thumbnail']); ?>" alt="Preview">
                        <div class="upload-placeholder d-none" id="thumbnailPlaceholder">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                            <p class="mb-1">Kéo thả hoặc click để chọn ảnh</p>
                            <small class="text-muted">PNG, JPG, JPEG (Tối đa 5MB)</small>
                        </div>
                    @endif
                        <div class="upload-placeholder" id="thumbnailPlaceholder">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                            <p class="mb-1">Kéo thả hoặc click để chọn ảnh</p>
                            <small class="text-muted">PNG, JPG, JPEG (Tối đa 5MB)</small>
                        </div>
                        <img id="thumbnailPreview" class="upload-preview d-none" alt="Preview">
                    @endif
                </div>
                <small class="text-muted">Click để thay đổi ảnh poster</small>
            </div>
            
            <!-- Upload Banner -->
            <div class="col-md-6 mb-3">
                <label for="banner_file" class="form-label">Banner</label>
                <div class="upload-box" id="bannerUploadBox" onclick="document.getElementById('banner_file').click()">
                    <input type="file" class="form-control d-none" id="banner_file" name="banner_file" accept="image/*" onchange="previewImage(this, 'bannerPreview', 'bannerUploadBox')">
                    @if (!empty($movie['banner']))
                        <img id="bannerPreview" class="upload-preview" src="<?php echo htmlspecialchars($movie['banner']); ?>" alt="Preview">
                        <div class="upload-placeholder d-none" id="bannerPlaceholder">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                            <p class="mb-1">Kéo thả hoặc click để chọn ảnh</p>
                            <small class="text-muted">PNG, JPG, JPEG (Tối đa 5MB)</small>
                        </div>
                    @else
                        <div class="upload-placeholder" id="bannerPlaceholder">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                            <p class="mb-1">Kéo thả hoặc click để chọn ảnh</p>
                            <small class="text-muted">PNG, JPG, JPEG (Tối đa 5MB)</small>
                        </div>
                        <img id="bannerPreview" class="upload-preview d-none" alt="Preview">
                    @endif
                </div>
                <small class="text-muted">Click để thay đổi ảnh banner</small>
            </div>
            
            <!-- Upload Video (chỉ hiện cho phim lẻ) -->
            <div class="col-md-6 mb-3" id="videoSection" style="display: <?php echo (($movie['type'] ?? 'phimle') == 'phimle') ? 'block' : 'none'; ?>;">
                <label for="video_file" class="form-label">Video phim</label>
                <div class="upload-box video-upload" id="videoUploadBox" onclick="document.getElementById('video_file').click()">
                    <input type="file" class="form-control d-none" id="video_file" name="video_file" accept="video/*" onchange="previewVideo(this)">
                    @if (!empty($movie['video_url']))
                        <div id="videoInfo">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="mb-1 video-name"><?php echo basename($movie['video_url']); ?></p>
                            <small class="text-muted video-size">Video hiện tại</small>
                        </div>
                        <div class="upload-placeholder d-none" id="videoPlaceholder">
                            <i class="fas fa-film fa-3x text-muted mb-2"></i>
                            <p class="mb-1">Kéo thả hoặc click để chọn video</p>
                            <small class="text-muted">MP4, AVI, MOV, MKV (Tối đa 500MB)</small>
                        </div>
                    @else
                        <div class="upload-placeholder" id="videoPlaceholder">
                            <i class="fas fa-film fa-3x text-muted mb-2"></i>
                            <p class="mb-1">Kéo thả hoặc click để chọn video</p>
                            <small class="text-muted">MP4, AVI, MOV, MKV (Tối đa 500MB)</small>
                        </div>
                        <div id="videoInfo" class="d-none">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="mb-1 video-name"></p>
                            <small class="text-muted video-size"></small>
                        </div>
                    @endif
                </div>
                <small class="text-muted">Click để thay đổi video phim</small>
                @if (!empty($movie['video_url']))
                    <div class="mt-2">
                        <small class="text-muted">Video hiện tại: 
                            <?php if (strpos($movie['video_url'], 'http') === 0): ?>
                                <a href="<?php echo htmlspecialchars($movie['video_url']); ?>" target="_blank"><?php echo htmlspecialchars($movie['video_url']); ?></a>
                            <?php else: ?>
                                <?php echo htmlspecialchars($movie['video_url']); ?>
                            <?php endif; ?>
                        </small>
                    </div>
                @endif
            </div>
            
            <!-- Thông báo cho phim bộ -->
            <div class="col-md-6 mb-3" id="videoSeriesNotice" style="display: <?php echo (($movie['type'] ?? 'phimle') == 'phimbo') ? 'block' : 'none'; ?>;">
                <label class="form-label">Video phim</label>
                <div class="alert alert-info mb-0" style="min-height: 120px; display: flex; align-items: center;">
                    <div>
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Phim bộ:</strong> Video được quản lý theo từng tập ở phần "Quản lý tập phim bộ" bên dưới.
                    </div>
                </div>
            </div>
            
            <!-- Upload Trailer -->
            <div class="col-md-6 mb-3">
                <label for="trailer_file" class="form-label">Trailer</label>
                <div class="upload-box video-upload" id="trailerUploadBox" onclick="document.getElementById('trailer_file').click()">
                    <input type="file" class="form-control d-none" id="trailer_file" name="trailer_file" accept="video/*" onchange="previewTrailer(this)">
                    @if (!empty($movie['trailer_url']) && strpos($movie['trailer_url'], 'http') !== 0)
                        <div id="trailerInfo">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="mb-1 trailer-name"><?php echo basename($movie['trailer_url']); ?></p>
                            <small class="text-muted trailer-size">Trailer hiện tại</small>
                        </div>
                        <div class="upload-placeholder d-none" id="trailerPlaceholder">
                            <i class="fas fa-play-circle fa-3x text-muted mb-2"></i>
                            <p class="mb-1">Kéo thả hoặc click để chọn trailer</p>
                            <small class="text-muted">MP4, AVI, MOV (Tối đa 100MB)</small>
                        </div>
                    @else
                        <div class="upload-placeholder" id="trailerPlaceholder">
                            <i class="fas fa-play-circle fa-3x text-muted mb-2"></i>
                            <p class="mb-1">Kéo thả hoặc click để chọn trailer</p>
                            <small class="text-muted">MP4, AVI, MOV (Tối đa 100MB)</small>
                        </div>
                        <div id="trailerInfo" class="d-none">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="mb-1 trailer-name"></p>
                            <small class="text-muted trailer-size"></small>
                        </div>
                    @endif
                </div>
                <small class="text-muted mt-1 d-block">Hoặc nhập URL YouTube:</small>
                <input type="url" class="form-control mt-1" id="trailer_url" name="trailer_url" value="<?php echo htmlspecialchars($movie['trailer_url'] ?? ''); ?>" placeholder="https://youtube.com/watch?v=...">
            </div>
        </div>
        
        <!-- Phần quản lý tập phim bộ (hiện khi chọn "Phim bộ") -->
        <div id="seriesSection" style="display: <?php echo (($movie['type'] ?? 'phimle') == 'phimbo') ? 'block' : 'none'; ?>;">
            <hr class="my-4">
            <h6 class="mb-3"><i class="fas fa-list me-2"></i>Quản lý tập phim bộ</h6>
            
            <!-- Hiển thị các tập hiện có -->
            @if (!empty($episodes))
            <div class="mb-4">
                <h6 class="mb-3">Các tập hiện có:</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Số tập</th>
                                <th>Tiêu đề</th>
                                <th style="width: 200px;">Video hiện tại</th>
                                <th style="width: 250px;">Upload/Thay đổi video</th>
                                <th style="width: 100px;">Thời lượng</th>
                                <th style="width: 80px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($episodes as $episode)
                            <tr>
                                <td class="text-center fw-bold"><?php echo $episode['episode_number']; ?></td>
                                <td><?php echo htmlspecialchars($episode['title'] ?? 'Tập ' . $episode['episode_number']); ?></td>
                                <td>
                                    <?php if (!empty($episode['video_url'])): ?>
                                        <span class="text-success">
                                            <i class="fas fa-check-circle"></i> 
                                            <a href="<?php echo htmlspecialchars($episode['video_url']); ?>" target="_blank" class="text-success">
                                                <?php echo basename($episode['video_url']); ?>
                                            </a>
                                        </span>
                                    @else
                                        <span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Chưa có video</span>
                                    @endif
                                </td>
                                <td>
                                    <input type="file" class="form-control form-control-sm" 
                                           name="episode_video_<?php echo $episode['id']; ?>" 
                                           accept="video/*"
                                           data-episode-id="<?php echo $episode['id']; ?>">
                                    <small class="text-muted">Chọn file để thay đổi video</small>
                                </td>
                                <td class="text-center"><?php echo $episode['duration'] ? $episode['duration'] . ' phút' : 'N/A'; ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteEpisode(<?php echo $episode['id']; ?>)" title="Xóa tập">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            
            <!-- Thêm tập mới -->
            <div class="row mb-3">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Thêm tập mới</label>
                    <div class="border rounded p-3">
                        <div id="episodesContainer">
                            <!-- Các tập mới sẽ được thêm bởi JavaScript -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-3" onclick="addEpisodeInput()">
                            <i class="fas fa-plus"></i> Thêm tập
                        </button>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> Thêm các tập phim bộ mới. 
                        Mỗi tập cần có số tập (bắt buộc). Video file có thể thêm sau khi cần. 
                        <strong>Danh sách tập sẽ hiển thị ngay sau khi thêm, kể cả khi chưa có video.</strong>
                    </small>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="?route=admin/movies" class="btn btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Cập nhật
            </button>
        </div>
    </form>
</div>

@endsection