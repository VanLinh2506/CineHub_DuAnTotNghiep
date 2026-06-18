<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php if(isset($title)): ?>
            <?php echo e($title); ?><?php if($title !== 'CineHub'): ?> - <?php endif; ?>
        <?php endif; ?>
        CineHub
    </title>
    <link rel="icon" href="<?php echo e(storage_url('data/img/avt_webb.png')); ?>" type="image/png">
    
    <?php if(isset($meta_description)): ?>
        <meta name="description" content="<?php echo e($meta_description); ?>">
    <?php endif; ?>
    <?php if(isset($meta_keywords)): ?>
        <meta name="keywords" content="<?php echo e($meta_keywords); ?>">
    <?php endif; ?>
    <?php if(isset($meta_og_title)): ?>
        <meta property="og:title" content="<?php echo e($meta_og_title); ?>">
    <?php endif; ?>
    <?php if(isset($meta_og_description)): ?>
        <meta property="og:description" content="<?php echo e($meta_og_description); ?>">
    <?php endif; ?>
    <?php if(isset($meta_og_image)): ?>
        <meta property="og:image" content="<?php echo e($meta_og_image); ?>">
    <?php endif; ?>
    
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="CineHub">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('style.css')); ?>?v=<?php echo e(time()); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <?php
        $isAuthPage = Route::is('auth.*') || (request()->has('route') && (
            str_contains(request('route'), 'auth/login') || 
            str_contains(request('route'), 'auth/register')
        ));
    ?>
    
    <?php if(!$isAuthPage): ?>
        <?php echo $__env->make('components.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
    
    <main class="main-content">
        <?php echo $__env->yieldContent('content'); ?>
    </main>
    
    <?php if(!$isAuthPage): ?>
        <?php echo $__env->make('components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views/layouts/app.blade.php ENDPATH**/ ?>