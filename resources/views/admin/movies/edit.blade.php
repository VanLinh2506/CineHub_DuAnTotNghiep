@extends('admin.layout')

@section('content')

<style>
    /* Upload Box Styling */
    .upload-box {
        border: 2px dashed #cbd5e0;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: linear-gradient(145deg, #f7fafc 0%, #edf2f7 100%);
        min-height: 150px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .upload-box:hover {
        border-color: #667eea;
        background: linear-gradient(145deg, #f0f4ff 0%, #e8ecff 100%);
    }

    .upload-box.has-file {
        border-style: solid;
        border-color: #48bb78;
        background: linear-gradient(145deg, #f0fff4 0%, #e6ffed 100%);
    }

    .upload-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .upload-preview {
        max-width: 100%;
        max-height: 120px;
        border-radius: 8px;
        object-fit: contain;
    }

    .video-upload {
        min-height: 120px;
    }

    /* Episodes Table Styling */
    #seriesSection .table {
        background: #fff;
    }

    #seriesSection .table th {
        background: #f8f9fa;
        color: #333;
        font-weight: 600;
    }

    #seriesSection .table td {
        color: #333;
        vertical-align: middle;
    }

    #seriesSection .border {
        background: #fff;
    }

    #seriesSection h6 {
        color: #333;
    }

    #seriesSection .form-label {
        color: #333;
    }

    /* Episode Input Row Styling */
    .episode-item {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 15px;
        border: 1px solid #e0e0e0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .episode-item .form-label {
        color: #333 !important;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 8px;
        display: block;
    }

    .episode-item .form-control {
        background: #fff !important;
        color: #333 !important;
        border: 1px solid #ced4da !important;
        padding: 0.5rem 0.75rem !important;
        width: 100% !important;
        height: auto !important;
        min-height: 38px;
    }

    .episode-item input[type="number"].form-control {
        -moz-appearance: textfield;
        appearance: textfield;
    }

    .episode-item input[type="number"].form-control::-webkit-outer-spin-button,
    .episode-item input[type="number"].form-control::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .episode-item .form-control:focus {
        border-color: #667eea !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15) !important;
    }

    .episode-item .text-muted {
        color: #6c757d !important;
        font-size: 0.8rem;
    }

    .episode-item .btn-outline-danger {
        margin-top: 8px;
    }

    #episodesContainer .row {
        display: flex;
        flex-wrap: wrap;
    }

    #episodesContainer .col-md-2,
    #episodesContainer .col-md-4 {
        padding: 0 10px;
    }

    #seriesSection {
        background: #f8f9fa;
        padding: 25px;
        border-radius: 12px;
    }

    #seriesSection h6 {
        color: #333;
        font-weight: 600;
    }

    #seriesSection .form-label {
        color: #333;
    }

    #seriesSection .border {
        background: #fff;
        border-color: #e0e0e0 !important;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Sửa phim</h5>

    <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="stat-card">
    <form method="POST" action="{{ route('admin.movies.update') }}" enctype="multipart/form-data">

        @csrf

        <input type="hidden" name="id" value="{{ $movie['id'] }}">

        <div class="row">

            <div class="col-md-12 mb-3">
                <label for="title" class="form-label">
                    Tiêu đề phim
                    <span class="text-danger">*</span>
                </label>

                <input type="text" class="form-control" id="title" name="title"
                    value="{{ old('title', $movie['title']) }}" required>
            </div>

            @php
                $selectedCategoryIds = [];

                if (!empty($movieCategories)) {
                    $selectedCategoryIds = collect($movieCategories)
                        ->pluck('category_id')
                        ->toArray();
                } elseif (!empty($movie['category_id'])) {
                    $selectedCategoryIds = [$movie['category_id']];
                }
            @endphp

            <div class="col-md-6 mb-3">
                <label for="category_ids" class="form-label">
                    Thể loại
                    <small class="text-muted">
                        (có thể chọn nhiều)
                    </small>
                </label>

                <select class="form-select" id="category_ids" name="category_ids[]" multiple size="4">

                    @foreach($categories as $cat)
                        <option value="{{ $cat['id'] }}" {{ in_array($cat['id'], $selectedCategoryIds) ? 'selected' : '' }}>
                            {{ $cat['name'] }}
                        </option>
                    @endforeach

                </select>

                <small class="text-muted">
                    Giữ Ctrl (Windows) hoặc Cmd (Mac) để chọn nhiều thể loại
                </small>
            </div>

            <div class="col-md-3 mb-3">
                <label for="level" class="form-label">
                    Cấp độ
                </label>

                <select class="form-select" id="level" name="level">

                    <option value="Free" {{ $movie['level'] == 'Free' ? 'selected' : '' }}>
                        Free
                    </option>

                    <option value="Silver" {{ $movie['level'] == 'Silver' ? 'selected' : '' }}>
                        Silver
                    </option>

                    <option value="Gold" {{ $movie['level'] == 'Gold' ? 'selected' : '' }}>
                        Gold
                    </option>

                    <option value="Premium" {{ $movie['level'] == 'Premium' ? 'selected' : '' }}>
                        Premium
                    </option>

                </select>
            </div>

            <div class="col-md-3 mb-3">
                <label for="status_admin" class="form-label">
                    Trạng thái Admin
                </label>

                <select class="form-select" id="status_admin" name="status_admin">

                    <option value="draft" {{ ($movie['status_admin'] ?? 'draft') == 'draft' ? 'selected' : '' }}>
                        Draft
                    </option>

                    <option value="scheduled" {{ ($movie['status_admin'] ?? '') == 'scheduled' ? 'selected' : '' }}>
                        Scheduled
                    </option>

                    <option value="published" {{ ($movie['status_admin'] ?? '') == 'published' ? 'selected' : '' }}>
                        Published
                    </option>

                    <option value="archived" {{ ($movie['status_admin'] ?? '') == 'archived' ? 'selected' : '' }}>
                        Archived
                    </option>

                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label for="type" class="form-label">
                    Loại phim
                    <span class="text-danger">*</span>
                </label>

                <select class="form-select" id="type" name="type"
                    onchange="toggleSeriesSection(); toggleDurationField();">

                    <option value="phimle" {{ ($movie['type'] ?? 'phimle') == 'phimle' ? 'selected' : '' }}>
                        Phim lẻ
                    </option>

                    <option value="phimbo" {{ ($movie['type'] ?? '') == 'phimbo' ? 'selected' : '' }}>
                        Phim bộ
                    </option>

                </select>
            </div>

            <div class="col-md-3 mb-3">
                <label for="status" class="form-label">
                    Trạng thái
                </label>

                <select class="form-select" id="status" name="status"
                    onchange="toggleTheaterSection(); toggleDurationField();">

                    <option value="Sắp chiếu" {{ $movie['status'] == 'Sắp chiếu' ? 'selected' : '' }}>
                        Sắp chiếu
                    </option>

                    <option value="Chiếu rạp" {{ $movie['status'] == 'Chiếu rạp' ? 'selected' : '' }}>
                        Chiếu rạp
                    </option>

                    <option value="Chiếu online" {{ $movie['status'] == 'Chiếu online' ? 'selected' : '' }}>
                        Chiếu online
                    </option>

                </select>
            </div>

            {{-- Thời lượng chỉ hiện cho phim lẻ chiếu rạp --}}
            <div class="col-md-3 mb-3" id="durationSection"
                style="display: {{ (($movie['type'] ?? 'phimle') == 'phimle' && $movie['status'] == 'Chiếu rạp') ? 'block' : 'none' }};">

                <label for="duration" class="form-label">
                    Thời lượng (phút)
                </label>

                <input type="number" class="form-control" id="duration" name="duration"
                    value="{{ $movie['duration'] ?? '' }}" min="0">

                <small class="text-muted">
                    Chỉ áp dụng cho phim chiếu rạp
                </small>
            </div>

            <div class="col-md-3 mb-3">
                {{-- Placeholder giữ layout --}}
            </div>

            <div class="col-md-3 mb-3">
                <label for="age_rating" class="form-label">
                    Độ tuổi
                </label>

                <input type="text" class="form-control" id="age_rating" name="age_rating"
                    value="{{ old('age_rating', $movie['age_rating'] ?? '') }}" placeholder="VD: T18, P">
            </div>

            <div class="col-md-6 mb-3">
                <label for="director" class="form-label">
                    Đạo diễn
                </label>

                <input type="text" class="form-control" id="director" name="director"
                    value="{{ old('director', $movie['director'] ?? '') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="actors" class="form-label">
                    Diễn viên
                </label>

                <input type="text" class="form-control" id="actors" name="actors"
                    value="{{ old('actors', $movie['actors'] ?? '') }}" placeholder="VD: Diễn viên 1, Diễn viên 2">
            </div>

            <div class="col-md-6 mb-3">
                <label for="country" class="form-label">
                    Quốc gia
                </label>

                <input type="text" class="form-control" id="country" name="country"
                    value="{{ old('country', $movie['country'] ?? '') }}" placeholder="VD: Việt Nam, Mỹ">
            </div>

            <div class="col-md-6 mb-3">
                <label for="language" class="form-label">
                    Ngôn ngữ
                </label>

                <input type="text" class="form-control" id="language" name="language"
                    value="{{ old('language', $movie['language'] ?? '') }}" placeholder="VD: Tiếng Việt, Tiếng Anh">
            </div>

            <div class="col-md-12 mb-3">
                <label for="description" class="form-label">
                    Mô tả
                </label>

                <textarea class="form-control" id="description" name="description"
                    rows="4">{{ old('description', $movie['description'] ?? '') }}</textarea>
            </div>

            {{-- Upload Poster / Thumbnail --}}
            <div class="col-md-6 mb-3">

                <label for="thumbnail_file" class="form-label">
                    Poster / Thumbnail
                </label>

                <div class="upload-box" id="thumbnailUploadBox"
                    onclick="document.getElementById('thumbnail_file').click()">

                    <input type="file" class="form-control d-none" id="thumbnail_file" name="thumbnail_file"
                        accept="image/*" onchange="previewImage(this, 'thumbnailPreview', 'thumbnailUploadBox')">

                    @if(!empty($movie['thumbnail']))

                        <img id="thumbnailPreview" class="upload-preview" src="{{ $movie['thumbnail'] }}" alt="Preview">

                        <div class="upload-placeholder d-none" id="thumbnailPlaceholder">

                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>

                            <p class="mb-1">
                                Kéo thả hoặc click để chọn ảnh
                            </p>

                            <small class="text-muted">
                                PNG, JPG, JPEG (Tối đa 5MB)
                            </small>

                        </div>

                    @else

                        <div class="upload-placeholder" id="thumbnailPlaceholder">

                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>

                            <p class="mb-1">
                                Kéo thả hoặc click để chọn ảnh
                            </p>

                            <small class="text-muted">
                                PNG, JPG, JPEG (Tối đa 5MB)
                            </small>

                        </div>

                        <img id="thumbnailPreview" class="upload-preview d-none" alt="Preview">

                    @endif

                </div>

                <small class="text-muted">
                    Click để thay đổi ảnh poster
                </small>

            </div>

            {{-- Upload Banner --}}
            <div class="col-md-6 mb-3">

                <label for="banner_file" class="form-label">
                    Banner
                </label>

                <div class="upload-box" id="bannerUploadBox" onclick="document.getElementById('banner_file').click()">

                    <input type="file" class="form-control d-none" id="banner_file" name="banner_file" accept="image/*"
                        onchange="previewImage(this, 'bannerPreview', 'bannerUploadBox')">

                    @if(!empty($movie['banner']))

                        <img id="bannerPreview" class="upload-preview" src="{{ $movie['banner'] }}" alt="Preview">

                        <div class="upload-placeholder d-none" id="bannerPlaceholder">

                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>

                            <p class="mb-1">
                                Kéo thả hoặc click để chọn ảnh
                            </p>

                            <small class="text-muted">
                                PNG, JPG, JPEG (Tối đa 5MB)
                            </small>

                        </div>

                    @else

                        <div class="upload-placeholder" id="bannerPlaceholder">

                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>

                            <p class="mb-1">
                                Kéo thả hoặc click để chọn ảnh
                            </p>

                            <small class="text-muted">
                                PNG, JPG, JPEG (Tối đa 5MB)
                            </small>

                        </div>

                        <img id="bannerPreview" class="upload-preview d-none" alt="Preview">

                    @endif

                </div>

                <small class="text-muted">
                    Click để thay đổi ảnh banner
                </small>

            </div>
            {{-- Upload Video (chỉ hiện cho phim lẻ) --}}
            <div class="col-md-6 mb-3" id="videoSection"
                style="display: {{ (($movie['type'] ?? 'phimle') == 'phimle') ? 'block' : 'none' }};">

                <label for="video_file" class="form-label">
                    Video phim
                </label>

                <div class="upload-box video-upload" id="videoUploadBox"
                    onclick="document.getElementById('video_file').click()">

                    <input type="file" class="form-control d-none" id="video_file" name="video_file" accept="video/*"
                        onchange="previewVideo(this)">

                    @if(!empty($movie['video_url']))

                        <div id="videoInfo">

                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>

                            <p class="mb-1 video-name">
                                {{ basename($movie['video_url']) }}
                            </p>

                            <small class="text-muted video-size">
                                Video hiện tại
                            </small>

                        </div>

                        <div class="upload-placeholder d-none" id="videoPlaceholder">

                            <i class="fas fa-film fa-3x text-muted mb-2"></i>

                            <p class="mb-1">
                                Kéo thả hoặc click để chọn video
                            </p>

                            <small class="text-muted">
                                MP4, AVI, MOV, MKV (Tối đa 500MB)
                            </small>

                        </div>

                    @else

                        <div class="upload-placeholder" id="videoPlaceholder">

                            <i class="fas fa-film fa-3x text-muted mb-2"></i>

                            <p class="mb-1">
                                Kéo thả hoặc click để chọn video
                            </p>

                            <small class="text-muted">
                                MP4, AVI, MOV, MKV (Tối đa 500MB)
                            </small>

                        </div>

                        <div id="videoInfo" class="d-none">

                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>

                            <p class="mb-1 video-name"></p>

                            <small class="text-muted video-size"></small>

                        </div>

                    @endif

                </div>

                <small class="text-muted">
                    Click để thay đổi video phim
                </small>

                @if(!empty($movie['video_url']))
                    <div class="mt-2">

                        <small class="text-muted">
                            Video hiện tại:

                            @if(Str::startsWith($movie['video_url'], 'http'))

                                <a href="{{ $movie['video_url'] }}" target="_blank">
                                    {{ $movie['video_url'] }}
                                </a>

                            @else

                                {{ $movie['video_url'] }}

                            @endif

                        </small>

                    </div>
                @endif

            </div>

            {{-- Thông báo cho phim bộ --}}
            <div class="col-md-6 mb-3" id="videoSeriesNotice"
                style="display: {{ (($movie['type'] ?? 'phimle') == 'phimbo') ? 'block' : 'none' }};">

                <label class="form-label">
                    Video phim
                </label>

                <div class="alert alert-info mb-0" style="min-height: 120px; display:flex; align-items:center;">

                    <div>
                        <i class="fas fa-info-circle me-2"></i>

                        <strong>Phim bộ:</strong>

                        Video được quản lý theo từng tập ở phần
                        "Quản lý tập phim bộ" bên dưới.
                    </div>

                </div>

            </div>

            {{-- Upload Trailer --}}
            <div class="col-md-6 mb-3">

                <label for="trailer_file" class="form-label">
                    Trailer
                </label>

                <div class="upload-box video-upload" id="trailerUploadBox"
                    onclick="document.getElementById('trailer_file').click()">

                    <input type="file" class="form-control d-none" id="trailer_file" name="trailer_file"
                        accept="video/*" onchange="previewTrailer(this)">

                    @if(!empty($movie['trailer_url']) && !Str::startsWith($movie['trailer_url'], 'http'))

                        <div id="trailerInfo">

                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>

                            <p class="mb-1 trailer-name">
                                {{ basename($movie['trailer_url']) }}
                            </p>

                            <small class="text-muted trailer-size">
                                Trailer hiện tại
                            </small>

                        </div>

                        <div class="upload-placeholder d-none" id="trailerPlaceholder">

                            <i class="fas fa-play-circle fa-3x text-muted mb-2"></i>

                            <p class="mb-1">
                                Kéo thả hoặc click để chọn trailer
                            </p>

                            <small class="text-muted">
                                MP4, AVI, MOV (Tối đa 100MB)
                            </small>

                        </div>

                    @else

                        <div class="upload-placeholder" id="trailerPlaceholder">

                            <i class="fas fa-play-circle fa-3x text-muted mb-2"></i>

                            <p class="mb-1">
                                Kéo thả hoặc click để chọn trailer
                            </p>

                            <small class="text-muted">
                                MP4, AVI, MOV (Tối đa 100MB)
                            </small>

                        </div>

                        <div id="trailerInfo" class="d-none">

                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>

                            <p class="mb-1 trailer-name"></p>

                            <small class="text-muted trailer-size"></small>

                        </div>

                    @endif

                </div>

                <small class="text-muted mt-1 d-block">
                    Hoặc nhập URL YouTube:
                </small>

                <input type="url" class="form-control mt-1" id="trailer_url" name="trailer_url"
                    value="{{ old('trailer_url', $movie['trailer_url'] ?? '') }}"
                    placeholder="https://youtube.com/watch?v=...">

            </div>

        </div>

        {{-- PHẦN QUẢN LÝ PHIM BỘ --}}
        <div id="seriesSection" style="display: {{ (($movie['type'] ?? 'phimle') == 'phimbo') ? 'block' : 'none' }};">

            <hr class="my-4">

            <h6 class="mb-3">
                <i class="fas fa-list me-2"></i>
                Quản lý tập phim bộ
            </h6>

            @if(!empty($episodes))

                <div class="mb-4">

                    <h6 class="mb-3">
                        Các tập hiện có:
                    </h6>

                    <div class="table-responsive">

                        <table class="table table-bordered">

                            <thead>
                                <tr>
                                    <th style="width:80px;">Số tập</th>
                                    <th>Tiêu đề</th>
                                    <th style="width:200px;">Video hiện tại</th>
                                    <th style="width:250px;">Upload/Thay đổi video</th>
                                    <th style="width:100px;">Thời lượng</th>
                                    <th style="width:80px;">Thao tác</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($episodes as $episode)

                                                    <tr>

                                                        <td class="text-center fw-bold">
                                                            {{ $episode['episode_number'] }}
                                                        </td>

                                                        <td>
                                                            {{ $episode['title'] ?? ('Tập ' . $episode['episode_number']) }}
                                                        </td>

                                                        <td>

                                                            @if(!empty($episode['video_url']))

                                                                <span class="text-success">

                                                                    <i class="fas fa-check-circle"></i>

                                                                    <a href="{{ $episode['video_url'] }}" target="_blank" class="text-success">

                                                                        {{ basename($episode['video_url']) }}

                                                                    </a>

                                                                </span>

                                                            @else

                                                                <span class="text-warning">
                                                                    <i class="fas fa-exclamation-triangle"></i>
                                                                    Chưa có video
                                                                </span>

                                                            @endif

                                                        </td>

                                                        <td>

                                                            <input type="file" class="form-control form-control-sm"
                                                                name="episode_video_{{ $episode['id'] }}" accept="video/*"
                                                                data-episode-id="{{ $episode['id'] }}">

                                                            <small class="text-muted">
                                                                Chọn file để thay đổi video
                                                            </small>

                                                        </td>

                                                        <td class="text-center">

                                                            {{ $episode['duration']
                                    ? $episode['duration'] . ' phút'
                                    : 'N/A' }}

                                                        </td>

                                                        <td class="text-center">

                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="deleteEpisode({{ $episode['id'] }})" title="Xóa tập">

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
        </div>
    </form>
</div>

@push('scripts')
    <script>
        let episodeCount = 0;

        // Preview Image
        function previewImage(input, previewId, boxId) {
            const preview = document.getElementById(previewId);
            const placeholder = document.getElementById(previewId.replace('Preview', 'Placeholder'));
            const box = document.getElementById(boxId);

            if (input.files && input.files[0]) {
                const file = input.files[0];

                if (file.size > 5 * 1024 * 1024) {
                    alert('File ảnh quá lớn! Vui lòng chọn file nhỏ hơn 5MB.');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');

                    if (placeholder) {
                        placeholder.classList.add('d-none');
                    }

                    box.classList.add('has-file');
                };

                reader.readAsDataURL(file);
            }
        }

        // Preview Video
        function previewVideo(input) {
            const placeholder = document.getElementById('videoPlaceholder');
            const info = document.getElementById('videoInfo');
            const box = document.getElementById('videoUploadBox');

            if (input.files && input.files[0]) {
                const file = input.files[0];

                if (file.size > 500 * 1024 * 1024) {
                    alert('File video quá lớn! Vui lòng chọn file nhỏ hơn 500MB.');
                    input.value = '';
                    return;
                }

                info.querySelector('.video-name').textContent = file.name;
                info.querySelector('.video-size').textContent = formatFileSize(file.size);

                if (placeholder) {
                    placeholder.classList.add('d-none');
                }

                info.classList.remove('d-none');
                box.classList.add('has-file');
            }
        }

        // Preview Trailer
        function previewTrailer(input) {
            const placeholder = document.getElementById('trailerPlaceholder');
            const info = document.getElementById('trailerInfo');
            const box = document.getElementById('trailerUploadBox');

            if (input.files && input.files[0]) {
                const file = input.files[0];

                if (file.size > 100 * 1024 * 1024) {
                    alert('File trailer quá lớn! Vui lòng chọn file nhỏ hơn 100MB.');
                    input.value = '';
                    return;
                }

                info.querySelector('.trailer-name').textContent = file.name;
                info.querySelector('.trailer-size').textContent = formatFileSize(file.size);

                if (placeholder) {
                    placeholder.classList.add('d-none');
                }

                info.classList.remove('d-none');
                box.classList.add('has-file');
            }
        }

        // Format file size
        function formatFileSize(bytes) {
            if (bytes === 0) {
                return '0 Bytes';
            }

            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];

            const i = Math.floor(Math.log(bytes) / Math.log(k));

            return parseFloat(
                (bytes / Math.pow(k, i)).toFixed(2)
            ) + ' ' + sizes[i];
        }

        // Hiện/ẩn thời lượng
        function toggleDurationField() {
            const type = document.getElementById('type').value;
            const status = document.getElementById('status').value;
            const durationSection = document.getElementById('durationSection');

            if (type === 'phimle' && status === 'Chiếu rạp') {
                if (durationSection) {
                    durationSection.style.display = 'block';
                }
            } else {
                if (durationSection) {
                    durationSection.style.display = 'none';
                }
            }
        }

        // Hiện/ẩn khu vực phim bộ
        function toggleSeriesSection() {
            const type = document.getElementById('type').value;

            const section = document.getElementById('seriesSection');
            const videoSection = document.getElementById('videoSection');
            const videoSeriesNotice = document.getElementById('videoSeriesNotice');

            if (type === 'phimbo') {
                section.style.display = 'block';

                if (videoSection) {
                    videoSection.style.display = 'none';
                }

                if (videoSeriesNotice) {
                    videoSeriesNotice.style.display = 'block';
                }
            } else {
                section.style.display = 'none';

                if (videoSection) {
                    videoSection.style.display = 'block';
                }

                if (videoSeriesNotice) {
                    videoSeriesNotice.style.display = 'none';
                }
            }
        }

        // Thêm tập mới
        function addEpisodeInput() {
            episodeCount++;

            const container = document.getElementById('episodesContainer');

            const existingEpisodes = @json(
                collect($episodes ?? [])->pluck('episode_number')->toArray()
            );

            const maxExisting =
                existingEpisodes.length > 0
                    ? Math.max(...existingEpisodes)
                    : 0;

            const addedInputs =
                container.querySelectorAll('.episode-number');

            let maxAdded = 0;

            addedInputs.forEach(input => {
                const val = parseInt(input.value) || 0;

                if (val > maxAdded) {
                    maxAdded = val;
                }
            });

            const nextEpisode =
                Math.max(maxExisting, maxAdded) + 1;

            const episodeDiv = document.createElement('div');

            episodeDiv.className = 'row mb-3 episode-item';
            episodeDiv.id = 'episode-' + episodeCount;

            episodeDiv.innerHTML = `
                        <div class="col-md-2">
                            <label class="form-label">
                                Tập số
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="number"
                                class="form-control episode-number"
                                name="new_episode_number_${episodeCount}"
                                value="${nextEpisode}"
                                min="1"
                                required
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">
                                Tiêu đề tập
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                name="new_episode_title_${episodeCount}"
                                placeholder="VD: Tập ${nextEpisode}: Tên tập"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">
                                Video File
                            </label>

                            <input
                                type="file"
                                class="form-control"
                                name="new_episode_video_${episodeCount}"
                                accept="video/*"
                            >

                            <small class="text-muted">
                                Có thể thêm video sau.
                            </small>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">
                                &nbsp;
                            </label>

                            <button
                                type="button"
                                class="btn btn-outline-danger w-100"
                                onclick="removeEpisode(${episodeCount})"
                            >
                                <i class="fas fa-times"></i>
                                Xóa
                            </button>
                        </div>
                    `;

            container.appendChild(episodeDiv);
        }

        // Xóa tập mới thêm
        function removeEpisode(id) {
            const episode =
                document.getElementById('episode-' + id);

            if (episode) {
                episode.remove();
            }
        }

        // Xóa tập hiện có
        function deleteEpisode(episodeId) {
            if (
                confirm('Bạn có chắc chắn muốn xóa tập này?')
            ) {
                window.location.href =
                    "{{ route('admin.movies.delete-episode') }}" +
                    '?id=' + episodeId +
                    '&movie_id={{ $movie["id"] }}';
            }
        }

        // Placeholder cho lịch chiếu
        function toggleTheaterSection() {
            // Lịch chiếu rạp được quản lý bởi moderator
        }

        // Init
        document.addEventListener('DOMContentLoaded', function () {
            toggleTheaterSection();
            toggleSeriesSection();
            toggleDurationField();
        });
    </script>
@endpush