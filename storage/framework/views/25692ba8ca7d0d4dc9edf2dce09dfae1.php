<?php $__env->startSection('content'); ?>
<section class="section">
    <br>
    <div class="container">
        <div class="filter-bar">
            <form method="GET" class="search-form" action="<?php echo e(route('movies.index')); ?>">
                <?php if(isset($search) && $search): ?>
                <input type="hidden" name="search" value="<?php echo e($search); ?>">
                <?php endif; ?>

                <div class="filter-options">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small">Thể loại</label>
                            <select name="category" class="form-select form-select-sm">
                                <option value="">Tất cả thể loại</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cat['id']); ?>" <?php echo e((isset($categoryId) && $categoryId == $cat['id']) ? 'selected' : ''); ?>>
                                    <?php echo e($cat['name']); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Trạng thái</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Tất cả trạng thái</option>
                                <option value="Chiếu online" <?php echo e((isset($status) && $status === 'Chiếu online') ? 'selected' : ''); ?>>Chiếu online</option>
                                <option value="Sắp chiếu" <?php echo e((isset($status) && $status === 'Sắp chiếu') ? 'selected' : ''); ?>>Sắp chiếu</option>
                                <option value="Chiếu rạp" <?php echo e((isset($status) && $status === 'Chiếu rạp') ? 'selected' : ''); ?>>Chiếu rạp</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Loại phim</label>
                            <select name="type" class="form-select form-select-sm">
                                <option value="">Tất cả</option>
                                <option value="phimle" <?php echo e((isset($type) && $type === 'phimle') ? 'selected' : ''); ?>>Phim lẻ</option>
                                <option value="phimbo" <?php echo e((isset($type) && $type === 'phimbo') ? 'selected' : ''); ?>>Phim bộ</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Quốc gia</label>
                            <select name="country" class="form-select form-select-sm">
                                <option value="">Tất cả quốc gia</option>
                                <?php if(isset($countries) && $countries->isNotEmpty()): ?>
                                <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($c); ?>" <?php echo e((isset($country) && $country === $c) ? 'selected' : ''); ?>>
                                    <?php echo e($c); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Đánh giá tối thiểu</label>
                            <select name="min_rating" class="form-select form-select-sm">
                                <option value="">Tất cả</option>
                                <?php $__currentLoopData = [9 => '9.0+', 8 => '8.0+', 7 => '7.0+', 6 => '6.0+', 5 => '5.0+']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($val); ?>" <?php echo e((isset($minRating) && $minRating == $val) ? 'selected' : ''); ?>><?php echo e($label); ?> ⭐</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-filter"></i> Áp dụng bộ lọc
                        </button>
                        <a href="<?php echo e(route('movies.index')); ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-redo"></i> Xóa bộ lọc
                        </a>
                    </div>
                </div>
            </form>

            <div class="category-filter mt-3">
                <div class="category-tags">
                    <a href="<?php echo e(route('movies.index')); ?>"
                        class="category-tag <?php echo e((!isset($categoryId) || !$categoryId) ? 'active' : ''); ?>">
                        <i class="fas fa-th"></i> Tất cả
                    </a>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('movies.category', $cat['id'])); ?>"
                        class="category-tag <?php echo e((isset($categoryId) && $categoryId == $cat['id']) ? 'active' : ''); ?>">
                        <?php echo e($cat['name']); ?>

                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">
            <i class="fas fa-video"></i>
            <?php if($search): ?>
            Kết quả tìm kiếm: "<?php echo e($search); ?>"
            <?php if(!$movies->isEmpty()): ?>
            <span class="badge bg-primary"><?php echo e(count($movies)); ?> phim</span>
            <?php endif; ?>
            <?php elseif(isset($categoryId) && $categoryId): ?>
            <?php $cat = collect($categories)->firstWhere('id', $categoryId); ?>
            Phim <?php echo e($cat['name'] ?? ''); ?>

            <?php if(!$movies->isEmpty()): ?>
            <span class="badge bg-primary"><?php echo e(count($movies)); ?> phim</span>
            <?php endif; ?>
            <?php else: ?>
            Tất cả phim
            <?php if(!$movies->isEmpty()): ?>
            <span class="badge bg-primary"><?php echo e(count($movies)); ?> phim</span>
            <?php endif; ?>
            <?php endif; ?>
        </h2>

        <?php if($search && $movies->isEmpty()): ?>
        <div class="empty-state text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4>Không tìm thấy phim nào</h4>
            <p class="text-muted">Không có kết quả phù hợp với từ khóa: "<strong><?php echo e($search); ?></strong>"</p>
            <a href="<?php echo e(route('movies.index')); ?>" class="btn btn-primary mt-3">
                <i class="fas fa-redo"></i> Xem tất cả phim
            </a>
        </div>
        <?php elseif($movies->isEmpty()): ?>
        <div class="empty-state text-center py-5">
            <i class="fas fa-film fa-3x text-muted mb-3"></i>
            <h4>Chưa có phim nào</h4>
            <p class="text-muted">Hiện tại chưa có phim phù hợp với bộ lọc của bạn</p>
        </div>
        <?php else: ?>
        <div class="movie-grid">
            <?php $__currentLoopData = $movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="movie-card">
                <?php
                // Nếu phim chiếu rạp, link đến trang đặt vé; các phim còn lại qua introduce
                $movieUrl = ($movie['status'] === 'Chiếu rạp')
                ? route('booking.index', ['movie' => $movie['id']])
                : route('movies.introduce', $movie['id']);
                ?>
                <a href="<?php echo e($movieUrl); ?>">
                    <div class="movie-thumbnail">
                        <?php if($movie['thumbnail']): ?>
                        <img src="<?php echo e($movie['thumbnail']); ?>" alt="<?php echo e($movie['title']); ?>">
                        <?php else: ?>
                        <div class="movie-placeholder"><i class="fas fa-film"></i></div>
                        <?php endif; ?>
                        <div class="movie-overlay">
                            <?php if($movie['status'] === 'Chiếu rạp'): ?>
                            <i class="fas fa-ticket-alt"></i>
                            <?php else: ?>
                            <i class="fas fa-play"></i>
                            <?php endif; ?>
                        </div>
                        <?php if(($movie['type'] ?? 'phimle') === 'phimbo'): ?>
                        <div class="movie-badge" title="Số tập">
                            <?php echo e(isset($movie['episode_count']) && $movie['episode_count'] > 0 ? $movie['episode_count'] . ' tập' : '? tập'); ?>

                        </div>
                        <?php else: ?>
                        <span class="movie-level"><?php echo e($movie['level']); ?></span>
                        <?php endif; ?>
                    </div>
                </a>
                <div class="movie-info">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                        <a href="<?php echo e($movieUrl); ?>" style="flex:1;text-decoration:none;color:inherit;">
                            <h3><?php echo e($movie['title']); ?></h3>
                        </a>
                        <?php if(isset($user) && $user): ?>
                        <button class="favorite-btn-inline <?php echo e((isset($favorites) && in_array($movie['id'], $favorites)) ? 'active' : ''); ?>"
                            data-movie-id="<?php echo e($movie['id']); ?>"
                            onclick="event.preventDefault();event.stopPropagation();toggleFavorite(this,<?php echo e($movie['id']); ?>);">
                            <i class="fas fa-heart"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                    <p class="movie-meta">
                        <span><i class="fas fa-star"></i> <?php echo e(number_format($movie['rating'], 1)); ?></span>
                        <?php if($movie['type'] === 'phimbo'): ?>
                        <span><i class="fas fa-tv"></i> Phim bộ</span>
                        <?php else: ?>
                        <span><i class="fas fa-clock"></i> <?php echo e($movie['duration']); ?> phút</span>
                        <?php endif; ?>
                    </p>
                    <p class="movie-category">
                        <span class="movie-type-badge"><?php echo e(($movie['type'] ?? 'phimle') === 'phimbo' ? 'Phim bộ' : 'Phim lẻ'); ?></span>
                        <?php if($movie['category_name']): ?>
                        <span> • <?php echo e($movie['category_name'] ?? 'Chưa phân loại'); ?></span>
                        <?php endif; ?>
                    </p>
                    <?php if($movie['description']): ?>
                    <p class="movie-description"><?php echo e(mb_substr($movie['description'], 0, 100)); ?>...</p>
                    <?php endif; ?>
                    <?php if(isset($movie['status']) && $movie['status'] === 'Chiếu rạp'): ?>
                    <div class="mt-2">
                        <a href="<?php echo e(route('booking.index', ['movie' => $movie['id']])); ?>"
                            class="btn btn-primary btn-sm w-100"
                            style="background:#e50914;border:none;padding:8px 16px;border-radius:100px;text-decoration:none;display:inline-block;text-align:center;color:white;font-weight:500;">
                            <i class="fas fa-ticket-alt"></i> Đặt vé xem phim
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <?php if($movies->hasPages()): ?>
        <nav aria-label="Phân trang danh sách phim" class="mt-4">
            <?php echo e($movies->withQueryString()->links()); ?>

            <div class="text-center mt-3 text-muted">
                <small>
                    Hiển thị <?php echo e($movies->firstItem()); ?> - <?php echo e($movies->lastItem()); ?>

                    trong tổng số <?php echo e(number_format($movies->total())); ?> phim
                    (Trang <?php echo e($movies->currentPage()); ?>/<?php echo e($movies->lastPage()); ?>)
                </small>
            </div>
        </nav>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php $__env->startPush('scripts'); ?>
<script>
    function toggleFavorite(btn, movieId) {
        <?php if(!isset($user) || !$user): ?>
        if (confirm('Vui lòng đăng nhập để thêm vào yêu thích!')) {
            window.location.href = '<?php echo e(route('login')); ?>';
        }
        return;
        <?php endif; ?>

        btn.disabled = true;
        fetch('<?php echo e(route('movies.toggleFavorite')); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                },
                body: new URLSearchParams({ movie_id: movieId })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    btn.classList.toggle('active', data.favorite);
                } else {
                    alert(data.error || 'Có lỗi xảy ra');
                }
                btn.disabled = false;
            })
            .catch(() => {
                alert('Có lỗi xảy ra khi thêm vào yêu thích');
                btn.disabled = false;
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const filterSelects = document.querySelectorAll('.filter-options select');
        const searchForm = document.querySelector('.search-form');
        filterSelects.forEach(select => {
            let isFirstLoad = true;
            select.addEventListener('change', function() {
                if (!isFirstLoad && searchForm) searchForm.submit();
                isFirstLoad = false;
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\CineHub_DuAnTotNghiep\resources\views/movie/index.blade.php ENDPATH**/ ?>