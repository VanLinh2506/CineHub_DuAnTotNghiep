@extends('admin.layout')

@section('content')

<style>
    .movie-edit-page .stat-card {
        max-width: 100%;
        overflow-x: hidden;
    }
    .movie-edit-page .stat-card .row {
        margin-left: -6px;
        margin-right: -6px;
    }
    .movie-edit-page .stat-card .row > [class*="col-"] {
        padding-left: 6px;
        padding-right: 6px;
    }
    .movie-edit-page .mb-3 {
        margin-bottom: 0.6rem !important;
    }
    .movie-edit-page .form-label {
        margin-bottom: 3px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .movie-edit-page .form-control,
    .movie-edit-page .form-select {
        font-size: 0.85rem;
        padding: 5px 8px;
        height: auto;
        min-height: 30px;
    }
    .movie-edit-page textarea.form-control {
        min-height: 60px;
    }
    /* Upload Box Styling */
    .upload-box {
        border: 2px dashed #cbd5e0;
        border-radius: 8px;
        padding: 10px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: linear-gradient(145deg, #f7fafc 0%, #edf2f7 100%);
        min-height: 90px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .upload-box .fa-3x { font-size: 1.5rem; }
    .upload-box p { font-size: 0.8rem; margin-bottom: 2px; }
    .upload-box small { font-size: 0.7rem; }
    .upload-box:hover { border-color: #667eea; }
    .upload-box.has-file { border-style: solid; border-color: #48bb78; }
    .upload-preview { max-width: 100%; max-height: 80px; border-radius: 6px; object-fit: contain; }
    .video-upload { min-height: 80px; }
    #seriesSection .table { font-size: 0.8rem; }
    #seriesSection .table th { font-size: 0.75rem; background: #f8f9fa; }
    .episode-item { background: #fff; padding: 12px; border-radius: 8px; margin-bottom: 10px; border: 1px solid #e0e0e0; }
    .episode-item .form-label { font-size: 0.8rem; margin-bottom: 4px; }
    .episode-item .form-control { font-size: 0.8rem !important; padding: 0.35rem 0.5rem !important; min-height: 30px; }
    #seriesSection { background: #f8f9fa; padding: 16px; border-radius: 10px; }
</style>

<div class="movie-edit-page">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5>Sửa phim</h5>
    <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="stat-card">

    <form method="POST" action="{{ route('admin.movies.update', $movie->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" value="{{ $movie->id }}">
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Tiêu đề phim <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title" value="{{ old('title', $movie->title) }}" required>
            </div>

            @php
                $selectedCategoryIds = [];
                if (!empty($movieCategories)) {
                    $selectedCategoryIds = collect($movieCategories)->pluck('category_id')->toArray();
                } elseif (!empty($movie->category_id)) {
                    $selectedCategoryIds = [$movie->category_id];
                }
            @endphp

            <div class="col-md-6 mb-3">
                <label class="form-label">Thể loại <small class="text-muted">(chọn nhiều)</small></label>
                <select class="form-select" name="category_ids[]" multiple size="4">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ in_array($cat->id, $selectedCategoryIds) ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">Giữ Ctrl để chọn nhiều</small>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">Cấp độ</label>
                <select class="form-select" name="level">
                    <option value="Free" {{ ($movie->level ?? 'Free') == 'Free' ? 'selected' : '' }}>Free</option>
                    <option value="Silver" {{ ($movie->level ?? '') == 'Silver' ? 'selected' : '' }}>Silver</option>
                    <option value="Gold" {{ ($movie->level ?? '') == 'Gold' ? 'selected' : '' }}>Gold</option>
                    <option value="Premium" {{ ($movie->level ?? '') == 'Premium' ? 'selected' : '' }}>Premium</option>
                </select>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">Trạng thái Admin</label>
                <select class="form-select" name="status_admin">
                    <option value="draft" {{ ($movie->status_admin ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="scheduled" {{ ($movie->status_admin ?? '') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="published" {{ ($movie->status_admin ?? '') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="archived" {{ ($movie->status_admin ?? '') == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">Loại phim <span class="text-danger">*</span></label>
                <select class="form-select" name="type" onchange="toggleSeriesSection(); toggleDurationField();">
                    <option value="phimle" {{ ($movie->type ?? 'phimle') == 'phimle' ? 'selected' : '' }}>Phim lẻ</option>
                    <option value="phimbo" {{ ($movie->type ?? '') == 'phimbo' ? 'selected' : '' }}>Phim bộ</option>
                </select>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">Trạng thái</label>
                <select class="form-select" name="status" onchange="toggleDurationField();">
                    <option value="Sắp chiếu" {{ $movie->status == 'Sắp chiếu' ? 'selected' : '' }}>Sắp chiếu</option>
                    <option value="Chiếu rạp" {{ $movie->status == 'Chiếu rạp' ? 'selected' : '' }}>Chiếu rạp</option>
                    <option value="Chiếu online" {{ $movie->status == 'Chiếu online' ? 'selected' : '' }}>Chiếu online</option>
                </select>
            </div>

            <div class="col-md-3 mb-3" id="durationSection" style="display: {{ ($movie->type == 'phimle' && $movie->status == 'Chiếu rạp') ? 'block' : 'none' }};">
                <label class="form-label">Thời lượng (phút)</label>
                <input type="number" class="form-control" name="duration" value="{{ $movie->duration ?? '' }}" min="0">
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">Độ tuổi</label>
                <input type="text" class="form-control" name="age_rating" value="{{ old('age_rating', $movie->age_rating ?? '') }}" placeholder="VD: T18, P">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Đạo diễn</label>
                <input type="text" class="form-control" name="director" value="{{ old('director', $movie->director ?? '') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Diễn viên</label>
                <input type="text" class="form-control" name="actors" value="{{ old('actors', $movie->actors ?? '') }}" placeholder="VD: Diễn viên 1, Diễn viên 2">
            </div>

            <div class="col-md-6 mb-3">

                <label class="form-label">Quốc gia</label>
                <select class="form-select js-country-select" name="country" data-current="{{ old('country', $movie->country ?? '') }}">
                    <option value="">Đang tải...</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Ngôn ngữ</label>
                <select class="form-select js-language-select" name="language" data-current="{{ old('language', $movie->language ?? '') }}">
                    <option value="">Đang tải...</option>
                </select>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Mô tả</label>
                <textarea class="form-control" name="description" rows="3">{{ old('description', $movie->description ?? '') }}</textarea>
            </div>

            {{-- Poster --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">Poster / Thumbnail</label>
                <div class="upload-box" id="thumbnailUploadBox" onclick="document.getElementById('thumbnail_file').click()">
                    <input type="file" class="d-none" id="thumbnail_file" name="thumbnail_file" accept="image/*" onchange="previewImage(this, 'thumbnailPreview', 'thumbnailUploadBox')">
                    @if(!empty($movie->thumbnail))
                        <img id="thumbnailPreview" class="upload-preview" src="{{ $movie->thumbnail }}" alt="">
                        <div class="upload-placeholder d-none" id="thumbnailPlaceholder">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                            <p class="mb-1">Click để chọn ảnh</p>
                            <small class="text-muted">PNG, JPG (5MB)</small>
                        </div>
                    @else
                        <div class="upload-placeholder" id="thumbnailPlaceholder">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                            <p class="mb-1">Click để chọn ảnh</p>
                            <small class="text-muted">PNG, JPG (5MB)</small>
                        </div>
                        <img id="thumbnailPreview" class="upload-preview d-none" alt="">
                    @endif
                </div>
            </div>

            {{-- Banner --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">Banner</label>
                <div class="upload-box" id="bannerUploadBox" onclick="document.getElementById('banner_file').click()">
                    <input type="file" class="d-none" id="banner_file" name="banner_file" accept="image/*" onchange="previewImage(this, 'bannerPreview', 'bannerUploadBox')">
                    @if(!empty($movie->banner))
                        <img id="bannerPreview" class="upload-preview" src="{{ $movie->banner }}" alt="">
                        <div class="upload-placeholder d-none" id="bannerPlaceholder">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                            <p class="mb-1">Click để chọn ảnh</p>
                            <small class="text-muted">PNG, JPG (5MB)</small>
                        </div>
                    @else
                        <div class="upload-placeholder" id="bannerPlaceholder">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                            <p class="mb-1">Click để chọn ảnh</p>
                            <small class="text-muted">PNG, JPG (5MB)</small>
                        </div>
                        <img id="bannerPreview" class="upload-preview d-none" alt="">
                    @endif
                </div>
            </div>

            {{-- Video --}}
            <div class="col-md-6 mb-3" id="videoSection" style="display: {{ $movie->type == 'phimle' ? 'block' : 'none' }};">
                <label class="form-label">Video phim</label>
                <div class="upload-box video-upload" id="videoUploadBox" onclick="document.getElementById('video_file').click()">
                    <input type="file" class="d-none" id="video_file" name="video_file" accept="video/*" onchange="previewVideo(this)">
                    @if(!empty($movie->video_url))
                        <div id="videoInfo">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="mb-1 video-name">{{ basename($movie->video_url) }}</p>
                            <small class="text-muted">Video hiện tại</small>
                        </div>
                        <div class="upload-placeholder d-none" id="videoPlaceholder">
                            <i class="fas fa-film fa-3x text-muted mb-2"></i>
                            <p class="mb-1">Click để chọn video</p>
                            <small class="text-muted">MP4, AVI (500MB)</small>
                        </div>
                    @else
                        <div class="upload-placeholder" id="videoPlaceholder">
                            <i class="fas fa-film fa-3x text-muted mb-2"></i>
                            <p class="mb-1">Click để chọn video</p>
                            <small class="text-muted">MP4, AVI (500MB)</small>
                        </div>
                        <div id="videoInfo" class="d-none">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="mb-1 video-name"></p>
                            <small class="text-muted video-size"></small>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Thông báo phim bộ --}}
            <div class="col-md-6 mb-3" id="videoSeriesNotice" style="display: {{ $movie->type == 'phimbo' ? 'block' : 'none' }};">
                <label class="form-label">Video phim</label>
                <div class="alert alert-info mb-0 py-3" style="min-height: 80px; display:flex; align-items:center;">
                    <div><i class="fas fa-info-circle me-2"></i><strong>Phim bộ:</strong> Video quản lý theo từng tập bên dưới.</div>
                </div>
            </div>


            {{-- Trailer --}}
            <div class="col-md-6 mb-3" id="trailerSection">
                <label class="form-label">Trailer</label>
                <div class="upload-box video-upload" id="trailerUploadBox" onclick="document.getElementById('trailer_file').click()">
                    <input type="file" class="d-none" id="trailer_file" name="trailer_file" accept="video/*" onchange="previewTrailer(this)">
                    @if(!empty($movie->trailer_url) && !Str::startsWith($movie->trailer_url, 'http'))
                        <div id="trailerInfo">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="mb-1 trailer-name">{{ basename($movie->trailer_url) }}</p>
                            <small class="text-muted">Trailer hiện tại</small>
                        </div>
                        <div class="upload-placeholder d-none" id="trailerPlaceholder">
                            <i class="fas fa-play-circle fa-3x text-muted mb-2"></i>
                            <p class="mb-1">Click để chọn trailer</p>
                            <small class="text-muted">MP4, AVI (100MB)</small>
                        </div>
                    @else
                        <div class="upload-placeholder" id="trailerPlaceholder">
                            <i class="fas fa-play-circle fa-3x text-muted mb-2"></i>
                            <p class="mb-1">Click để chọn trailer</p>
                            <small class="text-muted">MP4, AVI (100MB)</small>
                        </div>
                        <div id="trailerInfo" class="d-none">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="mb-1 trailer-name"></p>
                            <small class="text-muted trailer-size"></small>
                        </div>
                    @endif
                </div>
                <small class="text-muted mt-1 d-block">Hoặc URL YouTube:</small>
                <input type="url" class="form-control mt-1" name="trailer_url" value="{{ old('trailer_url', $movie->trailer_url ?? '') }}" placeholder="https://youtube.com/watch?v=...">
            </div>
        </div>

        {{-- PHẦN QUẢN LÝ PHIM BỘ --}}
        <div id="seriesSection" style="display: {{ $movie->type == 'phimbo' ? 'block' : 'none' }};">
            <hr class="my-3">
            <h6 class="mb-3"><i class="fas fa-list me-2"></i>Quản lý tập phim bộ</h6>

            @if(!empty($episodes) && $episodes->count() > 0)
                <div class="mb-4">
                    <h6 class="mb-2">Các tập hiện có:</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:60px;">Số tập</th>
                                    <th>Tiêu đề</th>
                                    <th style="width:180px;">Video</th>
                                    <th style="width:200px;">Upload video</th>
                                    <th style="width:70px;">Thời lượng</th>
                                    <th style="width:60px;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($episodes as $episode)
                                <tr>
                                    <td class="text-center fw-bold">{{ $episode->episode_number }}</td>
                                    <td>{{ $episode->title ?? ('Tập ' . $episode->episode_number) }}</td>
                                    <td>
                                        @if(!empty($episode->video_url))
                                            <span class="text-success"><i class="fas fa-check-circle"></i> <a href="{{ $episode->video_url }}" target="_blank" class="text-success">{{ basename($episode->video_url) }}</a></span>
                                        @else
                                            <span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Chưa có video</span>
                                        @endif
                                    </td>
                                    <td>
                                        <input type="file" class="form-control form-control-sm" name="episode_video_{{ $episode->id }}" accept="video/*">
                                        <small class="text-muted">Chọn file để thay đổi</small>
                                    </td>
                                    <td class="text-center">{{ $episode->duration ? $episode->duration . ' ph' : 'N/A' }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteEpisode({{ $episode->id }})" title="Xóa"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif


            {{-- Thêm tập mới --}}
            <div class="mb-3">
                <h6 class="mb-2"><i class="fas fa-plus-circle me-2 text-success"></i>Thêm tập mới</h6>
                <div class="border rounded p-3">
                    <div id="episodesContainer"></div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addEpisodeInput()"><i class="fas fa-plus"></i> Thêm tập</button>
                </div>
                <small class="text-muted"><i class="fas fa-info-circle"></i> Nhấn "Lưu" để lưu tất cả thay đổi.</small>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu thay đổi</button>
        </div>
    </form>
</div>

@push('scripts')

<script>
let episodeCount = 0;
const COUNTRIES_DATA_URL = 'https://cdn.jsdelivr.net/npm/world-countries@5.1.0/countries.json';
const FALLBACK_COUNTRIES = ['Việt Nam', 'Trung Quốc', 'Hàn Quốc', 'Nhật Bản', 'Thái Lan', 'Mỹ', 'Anh', 'Pháp', 'Ấn Độ'];
const FALLBACK_LANGUAGES = ['Tiếng Việt', 'Tiếng Trung', 'Tiếng Hàn', 'Tiếng Nhật', 'Tiếng Thái', 'Tiếng Anh', 'Tiếng Pháp', 'Tiếng Hindi'];

function fillSelect(select, items, currentValue, placeholder) {
    if (!select) return;
    const normalizedItems = [...new Set(items.filter(Boolean))].sort((a, b) => a.localeCompare(b, 'vi'));
    if (currentValue && !normalizedItems.includes(currentValue)) normalizedItems.unshift(currentValue);
    select.innerHTML = '';
    select.appendChild(new Option(placeholder, ''));
    normalizedItems.forEach(item => { const opt = new Option(item, item); opt.selected = item === currentValue; select.appendChild(opt); });
}

async function loadCountryLanguageSelects() {
    const countrySelect = document.getElementById('country');
    const languageSelect = document.getElementById('language');
    const currentCountry = countrySelect?.dataset.current || '';
    const currentLanguage = languageSelect?.dataset.current || '';
    try {
        const resp = await fetch(COUNTRIES_DATA_URL);
        if (!resp.ok) throw new Error('fail');
        const countries = await resp.json();
        fillSelect(countrySelect, countries.map(c => c?.name?.common).filter(Boolean), currentCountry, 'Chọn quốc gia');
        fillSelect(languageSelect, countries.flatMap(c => Object.values(c?.languages || {})), currentLanguage, 'Chọn ngôn ngữ');
    } catch(e) {
        fillSelect(countrySelect, FALLBACK_COUNTRIES, currentCountry, 'Chọn quốc gia');
        fillSelect(languageSelect, FALLBACK_LANGUAGES, currentLanguage, 'Chọn ngôn ngữ');
    }
}

function previewImage(input, previewId, boxId) {
    const preview = document.getElementById(previewId);
    const placeholder = document.getElementById(previewId.replace('Preview', 'Placeholder'));
    const box = document.getElementById(boxId);
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 5*1024*1024) { alert('File quá lớn!'); input.value = ''; return; }
        const reader = new FileReader();
        reader.onload = function(e) { preview.src = e.target.result; preview.classList.remove('d-none'); if(placeholder) placeholder.classList.add('d-none'); box.classList.add('has-file'); };
        reader.readAsDataURL(file);
    }
}

function previewVideo(input) {
    const info = document.getElementById('videoInfo');
    const placeholder = document.getElementById('videoPlaceholder');
    const box = document.getElementById('videoUploadBox');
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 500*1024*1024) { alert('File quá lớn!'); input.value = ''; return; }
        info.querySelector('.video-name').textContent = file.name;
        info.querySelector('.video-size').textContent = formatFileSize(file.size);
        if(placeholder) placeholder.classList.add('d-none'); info.classList.remove('d-none'); box.classList.add('has-file');
    }
}

function previewTrailer(input) {
    const info = document.getElementById('trailerInfo');
    const placeholder = document.getElementById('trailerPlaceholder');
    const box = document.getElementById('trailerUploadBox');
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 100*1024*1024) { alert('File quá lớn!'); input.value = ''; return; }
        info.querySelector('.trailer-name').textContent = file.name;
        info.querySelector('.trailer-size').textContent = formatFileSize(file.size);
        if(placeholder) placeholder.classList.add('d-none'); info.classList.remove('d-none'); box.classList.add('has-file');
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024, sizes = ['Bytes','KB','MB','GB'];
    return parseFloat((bytes / Math.pow(k, Math.floor(Math.log(bytes)/Math.log(k)))).toFixed(2)) + ' ' + sizes[Math.floor(Math.log(bytes)/Math.log(k))];
}

function toggleDurationField() {
    const type = document.querySelector('[name="type"]').value;
    const status = document.querySelector('[name="status"]').value;
    const sec = document.getElementById('durationSection');
    if (sec) sec.style.display = (type === 'phimle' && status === 'Chiếu rạp') ? 'block' : 'none';
}

function toggleSeriesSection() {
    const type = document.querySelector('[name="type"]').value;
    const section = document.getElementById('seriesSection');
    const videoSection = document.getElementById('videoSection');
    const notice = document.getElementById('videoSeriesNotice');
    if (section) section.style.display = type === 'phimbo' ? 'block' : 'none';
    if (videoSection) videoSection.style.display = type === 'phimle' ? 'block' : 'none';
    if (notice) notice.style.display = type === 'phimbo' ? 'block' : 'none';
}

function addEpisodeInput() {
    episodeCount++;
    const container = document.getElementById('episodesContainer');
    @php
        $existingEpisodeNumbers = collect($episodes ?? [])->pluck('episode_number')->toArray();
    @endphp
    const existing = @json($existingEpisodeNumbers);
    const maxExisting = existing.length > 0 ? Math.max(...existing) : 0;
    const added = container.querySelectorAll('.episode-number');
    let maxAdded = 0;
    added.forEach(i => { const v = parseInt(i.value)||0; if(v>maxAdded) maxAdded=v; });
    const next = Math.max(maxExisting, maxAdded) + 1;
    const div = document.createElement('div');
    div.className = 'row mb-3 episode-item';
    div.id = 'episode-' + episodeCount;
    div.innerHTML = `<div class="col-md-2"><label class="form-label">Tập số <span class="text-danger">*</span></label><input type="number" class="form-control episode-number" name="new_episode_number_${episodeCount}" value="${next}" min="1" required></div><div class="col-md-4"><label class="form-label">Tiêu đề</label><input type="text" class="form-control" name="new_episode_title_${episodeCount}" placeholder="Tập ${next}"></div><div class="col-md-4"><label class="form-label">Video File</label><input type="file" class="form-control" name="new_episode_video_${episodeCount}" accept="video/*"><small class="text-muted">Có thể thêm sau</small></div><div class="col-md-2"><label class="form-label">&nbsp;</label><button type="button" class="btn btn-outline-danger w-100 btn-sm" onclick="removeEpisode(${episodeCount})"><i class="fas fa-times"></i> Xóa</button></div>`;
    container.appendChild(div);
}

function removeEpisode(id) {
    const el = document.getElementById('episode-' + id);
    if (el) el.remove();
}

function deleteEpisode(episodeId) {
    if (confirm('Xóa tập này?')) {
        fetch('/admin/movies/{{ $movie->id }}/episodes/' + episodeId, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        }).then(r => { if (r.ok || r.status === 302) window.location.reload(); else alert('Lỗi!'); }).catch(() => alert('Lỗi!'));
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadCountryLanguageSelects();
    toggleSeriesSection();
    toggleDurationField();
});
</script>
@endpush
@endsection
