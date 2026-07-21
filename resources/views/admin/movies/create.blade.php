@extends('admin.layout')

@section('content')

    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Thêm phim mới</h2>

        <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="stat-card movie-form-container">
        <form method="POST" action="{{ route('admin.movies.store') }}" enctype="multipart/form-data">

            @csrf

            <div class="row">

                {{-- Tiêu đề --}}
                <div class="col-md-12 mb-3">
                    <label for="title" class="form-label">
                        Tiêu đề phim <span class="text-danger">*</span>
                    </label>

                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                        value="{{ old('title') }}" required>

                    @error('title')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Thể loại --}}
                <div class="col-md-6 mb-3">
                    <label for="category_ids" class="form-label">
                        Thể loại
                        <small class="text-muted">(có thể chọn nhiều)</small>
                    </label>

                    <div class="category-picker" role="group" aria-label="Chọn thể loại phim">
                        @foreach($categories as $cat)
                            <input
                                type="checkbox"
                                class="category-picker-input"
                                id="category_{{ $cat->id }}"
                                name="category_ids[]"
                                value="{{ $cat->id }}"
                                @checked(in_array($cat->id, old('category_ids', [])))
                            >
                            <label class="category-picker-chip" for="category_{{ $cat->id }}">
                                {{ $cat->name }}
                            </label>
                        @endforeach
                    </div>

                    <small class="text-muted">
                        Bấm từng thể loại để chọn hoặc bỏ chọn, có thể chọn nhiều thể loại cùng lúc.
                    </small>
                </div>

                {{-- Level --}}
                <div class="col-md-3 mb-3">
                    <label for="level" class="form-label">Cấp độ</label>

                    <select class="form-select" id="level" name="level">
                        <option value="Free" @selected(old('level') == 'Free')>Free</option>
                        <option value="Silver" @selected(old('level') == 'Silver')>Silver</option>
                        <option value="Gold" @selected(old('level') == 'Gold')>Gold</option>
                        <option value="Premium" @selected(old('level') == 'Premium')>Premium</option>
                    </select>
                </div>

                {{-- Admin Status --}}
                <div class="col-md-3 mb-3">
                    <label for="status_admin" class="form-label">
                        Trạng thái Admin
                    </label>

                    <select class="form-select" id="status_admin" name="status_admin">
                        <option value="draft">Draft</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>

                {{-- Loại phim --}}
                <div class="col-md-3 mb-3">
                    <label for="type" class="form-label">
                        Loại phim <span class="text-danger">*</span>
                    </label>

                    <select class="form-select" id="type" name="type"
                        onchange="toggleSeriesSection(); toggleDurationField(); toggleOnlineSchedule();">

                        <option value="phimle">Phim lẻ</option>
                        <option value="phimbo">Phim bộ</option>

                    </select>
                </div>

                {{-- Trạng thái --}}
                <div class="col-md-3 mb-3" id="projectionFormatSection" style="display:none;">
                    <label for="projection_format" class="form-label">Định dạng phòng chiếu</label>
                    <select class="form-select" id="projection_format" name="projection_format">
                        <option value="2D" @selected(old('projection_format', '2D') === '2D')>2D</option>
                        <option value="3D" @selected(old('projection_format') === '3D')>3D</option>
                        <option value="4DX" @selected(old('projection_format') === '4DX')>4D / 4DX</option>
                    </select>
                    <small class="text-muted">Chỉ áp dụng cho phim chiếu rạp.</small>
                </div>

                {{-- Trạng thái --}}
                <div class="col-md-3 mb-3">
                    <label for="status" class="form-label">
                        Trạng thái
                    </label>

                    <select class="form-select" id="status" name="status"
                        onchange="toggleTheaterSection(); toggleDurationField(); toggleOnlineSchedule();">

                        <option value="Sắp chiếu">Sắp chiếu</option>
                        <option value="Chiếu rạp">Chiếu rạp</option>
                        <option value="Chiếu online">Chiếu online</option>

                    </select>
                </div>

                <div class="col-md-6 mb-3" id="onlineScheduleSection" style="display:none;">
                    <label for="publish_date" class="form-label">Ngày bắt đầu chiếu online <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control" id="publish_date" name="publish_date" value="{{ old('publish_date') }}">
                    <input type="hidden" name="scheduled_status" value="Chiếu online">
                    <small class="text-muted">Trước ngày này phim nằm trong mục Sắp chiếu; đến ngày sẽ tự chuyển sang Chiếu online.</small>
                </div>

                <!-- Thời lượng chỉ hiện cho phim chiếu rạp -->
                <div class="col-md-3 mb-3" id="durationSection" style="display: none;">
                    <label for="duration" class="form-label">Thời lượng (phút)</label>
                    <input type="number" class="form-control" id="duration" name="duration" min="0">
                    <small class="text-muted">Chỉ áp dụng cho phim chiếu rạp</small>
                </div>

                <div class="col-md-3 mb-3">
                    <!-- Placeholder để giữ layout -->
                </div>

                <div class="col-md-3 mb-3">
                    <label for="age_rating" class="form-label">Độ tuổi</label>
                    <input type="text" class="form-control" id="age_rating" name="age_rating" placeholder="VD: T18, P">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="director" class="form-label">Đạo diễn</label>
                    <input type="text" class="form-control" id="director" name="director" list="director-options" autocomplete="off">
                    <datalist id="director-options">@foreach($directorSuggestions as $name)<option value="{{ $name }}">@endforeach</datalist>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="actors" class="form-label">Diễn viên</label>
                    <input type="text" class="form-control" id="actors" name="actors" list="actor-options" autocomplete="off"
                        placeholder="VD: Diễn viên 1, Diễn viên 2">
                    <datalist id="actor-options">@foreach($actorSuggestions as $name)<option value="{{ $name }}">@endforeach</datalist>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="country" class="form-label">Quốc gia</label>
                    <input type="text" class="form-control" id="country" name="country" value="{{ old('country') }}" list="country-options" autocomplete="off" placeholder="Nhập hoặc tìm quốc gia">
                    <datalist id="country-options" data-current="{{ old('country') }}"></datalist>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="language" class="form-label">Ngôn ngữ</label>
                    <input type="text" class="form-control" id="language" name="language" value="{{ old('language') }}" list="language-options" autocomplete="off" placeholder="Nhập hoặc tìm ngôn ngữ">
                    <datalist id="language-options" data-current="{{ old('language') }}"></datalist>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                </div>

                <!-- Upload Poster/Thumbnail -->
                <div class="col-md-6 mb-3">
                    <label for="thumbnail_file" class="form-label">Poster/Thumbnail <span
                            class="text-danger">*</span></label>
                    <div class="upload-box" id="thumbnailUploadBox"
                        onclick="document.getElementById('thumbnail_file').click()">
                        <input type="file" class="form-control d-none" id="thumbnail_file" name="thumbnail_file"
                            accept="image/*" onchange="previewImage(this, 'thumbnailPreview', 'thumbnailUploadBox')">
                        <div class="upload-placeholder" id="thumbnailPlaceholder">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                            <p class="mb-1">Kéo thả hoặc click để chọn ảnh</p>
                            <small class="text-muted">PNG, JPG, JPEG (Tối đa 5MB)</small>
                        </div>
                        <img id="thumbnailPreview" class="upload-preview d-none" alt="Preview">
                    </div>
                </div>

                <!-- Upload Banner -->
                <div class="col-md-6 mb-3">
                    <label for="banner_file" class="form-label">Banner</label>
                    <div class="upload-box" id="bannerUploadBox" onclick="document.getElementById('banner_file').click()">
                        <input type="file" class="form-control d-none" id="banner_file" name="banner_file" accept="image/*"
                            onchange="previewImage(this, 'bannerPreview', 'bannerUploadBox')">
                        <div class="upload-placeholder" id="bannerPlaceholder">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                            <p class="mb-1">Kéo thả hoặc click để chọn ảnh</p>
                            <small class="text-muted">PNG, JPG, JPEG (Tối đa 5MB)</small>
                        </div>
                        <img id="bannerPreview" class="upload-preview d-none" alt="Preview">
                    </div>
                </div>

                <!-- Upload Video (chỉ hiện cho phim lẻ) -->
                <div class="col-md-6 mb-3" id="videoSection">
                    <label for="video_file" class="form-label">Video phim</label>
                    <div class="upload-box video-upload" id="videoUploadBox"
                        onclick="document.getElementById('video_file').click()">
                        <input type="file" class="form-control d-none" id="video_file" name="video_file" accept="video/*"
                            onchange="previewVideo(this)">
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
                    </div>
                </div>

                <!-- Thông báo cho phim bộ -->
                <div class="col-md-6 mb-3" id="videoSeriesNotice" style="display: none;">
                    <label class="form-label">Video phim</label>
                    <div class="alert alert-info mb-0" style="min-height: 150px; display: flex; align-items: center;">
                        <div>
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Phim bộ:</strong> Video được quản lý theo từng tập ở phần "Quản lý tập phim bộ" bên
                            dưới. Bạn có thể thêm video cho từng tập khi tạo tập mới.
                        </div>
                    </div>
                </div>

                <!-- Upload Trailer -->
                <div class="col-md-6 mb-3" id="trailerSection">
                    <label for="trailer_file" class="form-label">Trailer</label>
                    <div class="upload-box video-upload" id="trailerUploadBox"
                        onclick="document.getElementById('trailer_file').click()">
                        <input type="file" class="form-control d-none" id="trailer_file" name="trailer_file"
                            accept="video/*" onchange="previewTrailer(this)">
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
                    </div>
                    <small class="text-muted mt-1 d-block">Hoặc nhập URL YouTube:</small>
                    <input type="url" class="form-control mt-1" id="trailer_url" name="trailer_url"
                        placeholder="https://youtube.com/watch?v=...">
                </div>
            </div>

            <!-- Phần quản lý tập phim bộ (hiện khi chọn "Phim bộ") -->
            <div id="seriesSection" style="display: none;">
                <hr class="my-4">
                <h6 class="mb-3"><i class="fas fa-list me-2"></i>Quản lý tập phim bộ</h6>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label for="total_episodes" class="form-label">Tổng số tập <span
                                class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="total_episodes" name="total_episodes" min="1"
                            value="1">
                        <small class="text-muted">Số tập dự kiến của phim bộ</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="current_episode" class="form-label">Tập hiện tại <span
                                class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="current_episode" name="current_episode" min="1"
                            value="1">
                        <small class="text-muted">Tập mới nhất đã phát hành</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Thêm tập mới</label>
                        <div class="border rounded p-3">
                            <div id="episodesContainer">
                                <!-- Các tập sẽ được thêm bởi JavaScript -->
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
                <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary">Hủy</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        let episodeCount = 0;
        const COUNTRIES_DATA_URL = 'https://cdn.jsdelivr.net/npm/world-countries@5.1.0/countries.json';
        const FALLBACK_COUNTRIES = ['Việt Nam', 'Trung Quốc', 'Hàn Quốc', 'Nhật Bản', 'Thái Lan', 'Mỹ', 'Anh', 'Pháp', 'Ấn Độ'];
        const FALLBACK_LANGUAGES = ['Tiếng Việt', 'Tiếng Trung', 'Tiếng Hàn', 'Tiếng Nhật', 'Tiếng Thái', 'Tiếng Anh', 'Tiếng Pháp', 'Tiếng Hindi'];

        function fillSelect(select, items, currentValue, placeholder) {
            if (!select) return;

            const normalizedItems = [...new Set(items.filter(Boolean))].sort((a, b) => a.localeCompare(b, 'vi'));
            if (currentValue && !normalizedItems.includes(currentValue)) {
                normalizedItems.unshift(currentValue);
            }

            select.innerHTML = '';
            select.appendChild(new Option(placeholder, ''));

            normalizedItems.forEach(item => {
                const option = new Option(item, item);
                option.selected = item === currentValue;
                select.appendChild(option);
            });
        }

        async function loadCountryLanguageSelects() {
            const countrySelect = document.getElementById('country-options');
            const languageSelect = document.getElementById('language-options');
            const currentCountry = countrySelect?.dataset.current || '';
            const currentLanguage = languageSelect?.dataset.current || '';

            try {
                const response = await fetch(COUNTRIES_DATA_URL);
                if (!response.ok) throw new Error('Cannot load country data');

                const countries = await response.json();
                const countryNames = countries.map(country => country?.name?.common).filter(Boolean);
                const languageNames = countries.flatMap(country => Object.values(country?.languages || {}));

                fillSelect(countrySelect, countryNames, currentCountry, 'Chọn quốc gia');
                fillSelect(languageSelect, languageNames, currentLanguage, 'Chọn ngôn ngữ');
            } catch (error) {
                fillSelect(countrySelect, FALLBACK_COUNTRIES, currentCountry, 'Chọn quốc gia');
                fillSelect(languageSelect, FALLBACK_LANGUAGES, currentLanguage, 'Chọn ngôn ngữ');
            }
        }

        function syncEpisodeCounters() {
            const totalEpisodes = document.getElementById('total_episodes');
            const currentEpisode = document.getElementById('current_episode');
            const episodeTotal = document.querySelectorAll('#episodesContainer .episode-number').length;

            if (totalEpisodes) totalEpisodes.value = Math.max(episodeTotal, 1);
            if (currentEpisode) currentEpisode.value = Math.max(episodeTotal, 1);
        }

        // Preview Image
        function previewImage(input, previewId, boxId) {
            const preview = document.getElementById(previewId);
            const placeholder = document.getElementById(previewId.replace('Preview', 'Placeholder'));
            const box = document.getElementById(boxId);

            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Check file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File ảnh quá lớn! Vui lòng chọn file nhỏ hơn 5MB.');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                    placeholder.classList.add('d-none');
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

                // Check file size (max 500MB)
                if (file.size > 500 * 1024 * 1024) {
                    alert('File video quá lớn! Vui lòng chọn file nhỏ hơn 500MB.');
                    input.value = '';
                    return;
                }

                info.querySelector('.video-name').textContent = file.name;
                info.querySelector('.video-size').textContent = formatFileSize(file.size);
                placeholder.classList.add('d-none');
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

                // Check file size (max 100MB)
                if (file.size > 100 * 1024 * 1024) {
                    alert('File trailer quá lớn! Vui lòng chọn file nhỏ hơn 100MB.');
                    input.value = '';
                    return;
                }

                info.querySelector('.trailer-name').textContent = file.name;
                info.querySelector('.trailer-size').textContent = formatFileSize(file.size);
                placeholder.classList.add('d-none');
                info.classList.remove('d-none');
                box.classList.add('has-file');
            }
        }

        // Format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Drag and drop support
        document.addEventListener('DOMContentLoaded', function () {
            const uploadBoxes = document.querySelectorAll('.upload-box');

            uploadBoxes.forEach(box => {
                box.addEventListener('dragover', function (e) {
                    e.preventDefault();
                    this.classList.add('dragover');
                });

                box.addEventListener('dragleave', function (e) {
                    e.preventDefault();
                    this.classList.remove('dragover');
                });

                box.addEventListener('drop', function (e) {
                    e.preventDefault();
                    this.classList.remove('dragover');

                    const input = this.querySelector('input[type="file"]');
                    if (input && e.dataTransfer.files.length > 0) {
                        input.files = e.dataTransfer.files;
                        input.dispatchEvent(new Event('change'));
                    }
                });
            });
        });

        // Hiện/ẩn thời lượng dựa trên loại phim và trạng thái
        function toggleDurationField() {
            const type = document.getElementById('type').value;
            const status = document.getElementById('status').value;
            const durationSection = document.getElementById('durationSection');

            // Chỉ hiện thời lượng cho phim lẻ chiếu rạp
            if (type === 'phimle' && status === 'Chiếu rạp') {
                if (durationSection) durationSection.style.display = 'block';
            } else {
                if (durationSection) durationSection.style.display = 'none';
            }
        }

        function toggleOnlineSchedule() {
            const visible = document.getElementById('type').value === 'phimle'
                && document.getElementById('status').value === 'Sắp chiếu';
            const section = document.getElementById('onlineScheduleSection');
            const input = document.getElementById('publish_date');
            if (section) section.style.display = visible ? 'block' : 'none';
            if (input) input.required = visible;
            const videoSection = document.getElementById('videoSection');
            if (videoSection && visible) videoSection.style.display = 'none';
        }

        function toggleSeriesSection() {
            const type = document.getElementById('type').value;
            const statusSelect = document.getElementById('status');
            const theaterOption = statusSelect?.querySelector('option[value="Chiếu rạp"]');
            const section = document.getElementById('seriesSection');
            const totalEpisodes = document.getElementById('total_episodes');
            const currentEpisode = document.getElementById('current_episode');
            const videoSection = document.getElementById('videoSection');
            const videoSeriesNotice = document.getElementById('videoSeriesNotice');
            const trailerSection = document.getElementById('trailerSection');
            const trailerFile = document.getElementById('trailer_file');
            const trailerUrl = document.getElementById('trailer_url');

            if (type === 'phimbo') {
                if (theaterOption) theaterOption.disabled = true;
                if (statusSelect?.value === 'Chiếu rạp') statusSelect.value = 'Chiếu online';
                section.style.display = 'block';
                if (totalEpisodes) totalEpisodes.setAttribute('required', 'required');
                if (currentEpisode) currentEpisode.setAttribute('required', 'required');
                // Ẩn phần video phim, hiện thông báo
                if (videoSection) videoSection.style.display = 'none';
                if (videoSeriesNotice) videoSeriesNotice.style.display = 'block';
                if (trailerSection) trailerSection.style.display = 'none';
                if (trailerFile) {
                    trailerFile.value = '';
                    trailerFile.disabled = true;
                }
                if (trailerUrl) {
                    trailerUrl.value = '';
                    trailerUrl.disabled = true;
                }
            } else {
                if (theaterOption) theaterOption.disabled = false;
                section.style.display = 'none';
                if (totalEpisodes) totalEpisodes.removeAttribute('required');
                if (currentEpisode) currentEpisode.removeAttribute('required');
                // Hiện phần video phim, ẩn thông báo
                if (videoSection) videoSection.style.display = 'block';
                if (videoSeriesNotice) videoSeriesNotice.style.display = 'none';
                if (trailerSection) trailerSection.style.display = 'block';
                if (trailerFile) trailerFile.disabled = false;
                if (trailerUrl) trailerUrl.disabled = false;
            }

            syncEpisodeCounters();
        }

        function addEpisodeInput() {
            episodeCount++;
            const container = document.getElementById('episodesContainer');

            // Đếm số tập đã thêm trong container
            const addedInputs = container.querySelectorAll('.episode-number');
            let maxAdded = 0;
            addedInputs.forEach(input => {
                const val = parseInt(input.value) || 0;
                if (val > maxAdded) maxAdded = val;
            });

            // Số tập tiếp theo = max tập đã thêm + 1, hoặc 1 nếu chưa có
            const nextEpisode = maxAdded > 0 ? maxAdded + 1 : 1;

            const episodeDiv = document.createElement('div');
            episodeDiv.className = 'row mb-3 episode-item';
            episodeDiv.id = 'episode-' + episodeCount;

            episodeDiv.innerHTML = `
            <div class="col-md-2">
                <label class="form-label">Tập số <span class="text-danger">*</span></label>
                <input type="number" class="form-control episode-number" name="new_episode_number_${episodeCount}" 
                       value="${nextEpisode}" min="1" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tiêu đề tập</label>
                <input type="text" class="form-control" name="new_episode_title_${episodeCount}" 
                       placeholder="VD: Tập ${nextEpisode}: Tên tập">
            </div>
            <div class="col-md-4">
                <label class="form-label">Video File</label>
                <input type="file" class="form-control" name="new_episode_video_${episodeCount}" 
                       accept="video/*">
                <small class="text-muted">Có thể thêm video sau.</small>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-outline-danger w-100" onclick="removeEpisode(${episodeCount})">
                    <i class="fas fa-times"></i> Xóa
                </button>
            </div>
        `;

            container.appendChild(episodeDiv);
            syncEpisodeCounters();
        }

        function removeEpisode(id) {
            const episode = document.getElementById('episode-' + id);
            if (episode) {
                episode.remove();
                syncEpisodeCounters();
            }
        }

        function toggleTheaterSection() {
            // Lịch chiếu rạp được quản lý bởi admin rạp (moderator)
            // Admin tối cao không can thiệp vào lịch chiếu và giá vé
            const isTheater = document.getElementById('status').value === 'Chiếu rạp';
            const section = document.getElementById('projectionFormatSection');
            const input = document.getElementById('projection_format');
            if (section) section.style.display = isTheater ? 'block' : 'none';
            if (input) input.disabled = !isTheater;
        }

        document.addEventListener('DOMContentLoaded', function () {
            loadCountryLanguageSelects();
            toggleTheaterSection();
            toggleSeriesSection();
            toggleDurationField();
            toggleOnlineSchedule();
            syncEpisodeCounters();
        });
    </script>
@endpush
