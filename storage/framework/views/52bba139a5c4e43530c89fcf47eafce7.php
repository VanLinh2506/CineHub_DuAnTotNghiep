<?php $__env->startSection('content'); ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Import tập phim từ folder</h2>

        <a href="<?php echo e(route('admin.movies.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <?php if(empty($folders)): ?>
        <div class="stat-card">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Không tìm thấy folder nào trong
                <code>data/phim/phimbo/</code>.

                <br>

                Vui lòng tạo folder và thêm các file video vào đó.
            </div>
        </div>
    <?php else: ?>

        <?php $__currentLoopData = $folders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $folder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <div class="stat-card mb-3">
                <h6 class="mb-3">
                    <i class="fas fa-folder"></i>
                    Folder:
                    <strong><?php echo e($folder['name']); ?></strong>

                    <span class="badge bg-primary ms-2">
                        <?php echo e($folder['count']); ?> file
                    </span>
                </h6>

                <form method="POST" action="<?php echo e(route('admin.movies.importEpisodes')); ?>"
                    id="importForm_<?php echo e(Str::slug($folder['name'])); ?>">

                    <?php echo csrf_field(); ?>

                    <input type="hidden" name="folder_name" value="<?php echo e($folder['name']); ?>">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                Chọn phim bộ
                                <span class="text-danger">*</span>
                            </label>

                            <select name="movie_id" class="form-select" required>
                                <option value="">
                                    -- Chọn phim --
                                </option>

                                <?php $__currentLoopData = $movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($movie->id ?? $movie['id']); ?>">
                                        <?php echo e($movie->title ?? $movie['title']); ?>

                                        (ID:
                                        <?php echo e($movie->id ?? $movie['id']); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>

                            <small class="text-muted">
                                Chọn phim bộ để import các tập
                            </small>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">Import</th>
                                    <th width="100">Số tập</th>
                                    <th>Tên file</th>
                                    <th width="120">Kích thước</th>
                                    <th width="100">Thao tác</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php $__currentLoopData = $folder['files']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <?php
                                        $filename = strtolower($file['name']);

                                        if (preg_match('/(?:tap|episode|ep)[_ ]?(\d+)/i', $filename, $matches)) {
                                            $episodeNumber = intval($matches[1]);
                                        } else {
                                            $episodeNumber = $index + 1;
                                        }

                                        $size = $file['size'];

                                        if ($size < 1024) {
                                            $formattedSize = $size . ' B';
                                        } elseif ($size < 1024 * 1024) {
                                            $formattedSize = number_format($size / 1024, 2) . ' KB';
                                        } else {
                                            $formattedSize = number_format($size / (1024 * 1024), 2) . ' MB';
                                        }
                                    ?>

                                    <tr>
                                        <td>
                                            <input type="checkbox" name="files[<?php echo e($index); ?>][import]" value="1" checked
                                                onchange="toggleEpisodeNumber(<?php echo e($index); ?>, this.checked)">
                                        </td>

                                        <td>
                                            <input type="number" name="files[<?php echo e($index); ?>][episode_number]"
                                                class="form-control form-control-sm" min="1" value="<?php echo e($episodeNumber); ?>" required>
                                        </td>

                                        <td>
                                            <input type="hidden" name="files[<?php echo e($index); ?>][path]" value="<?php echo e($file['path']); ?>">

                                            <code><?php echo e($file['name']); ?></code>
                                        </td>

                                        <td>
                                            <?php echo e($formattedSize); ?>

                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="editEpisodeTitle(<?php echo e($index); ?>)">
                                                <i class="fas fa-edit"></i>
                                                Sửa
                                            </button>
                                        </td>
                                    </tr>

                                    <tr id="titleRow_<?php echo e($index); ?>" style="display:none;">
                                        <td colspan="5">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">
                                                    Tên tập:
                                                </span>

                                                <input type="text" name="files[<?php echo e($index); ?>][title]" class="form-control"
                                                    id="titleInput_<?php echo e($index); ?>" placeholder="Ví dụ: Tập 1 - Khởi đầu">

                                                <button type="button" class="btn btn-outline-secondary"
                                                    onclick="saveEpisodeTitle(<?php echo e($index); ?>)">
                                                    <i class="fas fa-check"></i>
                                                    Lưu
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i>
                            Import các tập đã chọn
                        </button>

                        <button type="button" class="btn btn-outline-secondary"
                            onclick="selectAllFiles('<?php echo e(Str::slug($folder['name'])); ?>')">
                            <i class="fas fa-check-square"></i>
                            Chọn tất cả
                        </button>

                        <button type="button" class="btn btn-outline-secondary"
                            onclick="deselectAllFiles('<?php echo e(Str::slug($folder['name'])); ?>')">
                            <i class="fas fa-square"></i>
                            Bỏ chọn tất cả
                        </button>
                    </div>

                </form>
            </div>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function toggleEpisodeNumber(index, checked) {
            const numberInput =
                document.querySelector(
                    `input[name="files[${index}][episode_number]"]`
                );

            if (numberInput) {
                numberInput.disabled = !checked;
                numberInput.required = checked;
            }
        }

        function editEpisodeTitle(index) {
            const titleRow =
                document.getElementById(`titleRow_${index}`);

            const titleInput =
                document.getElementById(`titleInput_${index}`);

            if (titleRow && titleInput) {
                titleRow.style.display =
                    titleRow.style.display === 'none'
                        ? 'table-row'
                        : 'none';

                if (titleRow.style.display !== 'none') {
                    titleInput.focus();
                }
            }
        }

        function saveEpisodeTitle(index) {
            editEpisodeTitle(index);
        }

        function selectAllFiles(folderName) {
            const form =
                document.getElementById(
                    `importForm_${folderName}`
                );

            if (!form) return;

            const checkboxes =
                form.querySelectorAll(
                    'input[type="checkbox"][name*="[import]"]'
                );

            checkboxes.forEach(cb => {
                cb.checked = true;

                const match = cb.name.match(/\[(\d+)\]/);

                if (match) {
                    toggleEpisodeNumber(
                        parseInt(match[1]),
                        true
                    );
                }
            });
        }

        function deselectAllFiles(folderName) {
            const form =
                document.getElementById(
                    `importForm_${folderName}`
                );

            if (!form) return;

            const checkboxes =
                form.querySelectorAll(
                    'input[type="checkbox"][name*="[import]"]'
                );

            checkboxes.forEach(cb => {
                cb.checked = false;

                const match = cb.name.match(/\[(\d+)\]/);

                if (match) {
                    toggleEpisodeNumber(
                        parseInt(match[1]),
                        false
                    );
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes =
                document.querySelectorAll(
                    'input[type="checkbox"][name*="[import]"]'
                );

            checkboxes.forEach(cb => {
                const match = cb.name.match(/\[(\d+)\]/);

                if (match) {
                    toggleEpisodeNumber(
                        parseInt(match[1]),
                        cb.checked
                    );
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\admin\movies\scan-episodes.blade.php ENDPATH**/ ?>