<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($post->title); ?> - CineHub</title>
    <meta name="description" content="<?php echo e($post->excerpt); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .prose-content h1, .prose-content h2, .prose-content h3 { color: #facc15; margin: 1.5rem 0 0.75rem; font-weight: 700; }
        .prose-content h2 { font-size: 1.4rem; }
        .prose-content h3 { font-size: 1.2rem; }
        .prose-content p  { color: #d1d5db; line-height: 1.8; margin-bottom: 1rem; }
        .prose-content a  { color: #facc15; text-decoration: underline; }
        .prose-content img { max-width: 100%; border-radius: 0.5rem; margin: 1rem 0; }
        .prose-content ul, .prose-content ol { color: #d1d5db; padding-left: 1.5rem; margin-bottom: 1rem; }
        .prose-content li { margin-bottom: 0.3rem; }
        .prose-content blockquote { border-left: 3px solid #facc15; padding-left: 1rem; color: #9ca3af; font-style: italic; }
    </style>
</head>
<body class="bg-gray-950 text-white min-h-screen">

<div class="max-w-3xl mx-auto px-4 py-10">

    
    <nav class="text-sm text-gray-500 mb-6">
        <a href="<?php echo e(route('news.index')); ?>" class="hover:text-yellow-400">Tin tức</a>
        <?php if($post->category): ?>
            <span class="mx-2">/</span>
            <a href="<?php echo e(route('news.index', ['category' => $post->news_category_id])); ?>" class="hover:text-yellow-400">
                <?php echo e($post->category->name); ?>

            </a>
        <?php endif; ?>
        <span class="mx-2">/</span>
        <span class="text-gray-300"><?php echo e(Str::limit($post->title, 40)); ?></span>
    </nav>

    
    <h1 class="text-3xl font-bold leading-tight text-white mb-4"><?php echo e($post->title); ?></h1>

    
    <div class="flex items-center gap-4 text-sm text-gray-500 mb-6">
        <?php if($post->author): ?>
        <span>✍️ <?php echo e($post->author->name); ?></span>
        <?php endif; ?>
        <span>📅 <?php echo e($post->published_at->format('d/m/Y')); ?></span>
        <?php if($post->category): ?>
        <span class="text-yellow-400"><?php echo e($post->category->name); ?></span>
        <?php endif; ?>
    </div>

    
    <?php if($post->thumbnail): ?>
    <img src="<?php echo e($post->thumbnail); ?>" alt="<?php echo e($post->title); ?>"
         class="w-full rounded-xl mb-8 max-h-80 object-cover">
    <?php endif; ?>

    
    <div class="prose-content">
        <?php echo $post->content; ?>

    </div>

    
    <?php if($related->isNotEmpty()): ?>
    <div class="mt-14 border-t border-gray-800 pt-8">
        <h3 class="text-xl font-bold text-yellow-400 mb-5">Bài viết liên quan</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php $__currentLoopData = $related; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('news.show', $item->slug)); ?>"
               class="flex gap-3 bg-gray-900 rounded-lg p-3 hover:ring-1 hover:ring-yellow-400 transition">
                <?php if($item->thumbnail): ?>
                <img src="<?php echo e($item->thumbnail); ?>" class="w-20 h-16 object-cover rounded flex-shrink-0">
                <?php endif; ?>
                <div>
                    <p class="text-sm font-medium text-white line-clamp-2"><?php echo e($item->title); ?></p>
                    <p class="text-xs text-gray-500 mt-1"><?php echo e($item->published_at->format('d/m/Y')); ?></p>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="mt-10">
        <a href="<?php echo e(route('news.index')); ?>" class="text-yellow-400 hover:underline text-sm">← Quay lại tin tức</a>
    </div>

</div>
</body>
</html>
<?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\news\show.blade.php ENDPATH**/ ?>