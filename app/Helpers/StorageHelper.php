<?php

if (!function_exists('old_to_new_path')) {
    /**
     * Convert old data paths to new storage paths
     * 
     * @param string|null $oldPath
     * @return string|null
     */
    function old_to_new_path(?string $oldPath): ?string
    {
        if (empty($oldPath)) return null;
        
        $replacements = [
            'data/img/posters/' => 'posters/',
            'data/img/banners/' => 'banners/',
            'data/img/avatars/' => 'avatars/',
            'data/img/theaters/' => 'theaters/',
            'data/img/food/' => 'food/',
            'data/phim/phimle/' => 'movies/phimle/',
            'data/phim/phimbo/' => 'movies/phimbo/',
            'data/phim/trailers/' => 'movies/trailers/',
            '../data/img/posters/' => 'posters/',
            '../data/img/banners/' => 'banners/',
            '../data/img/avatars/' => 'avatars/',
            '../data/img/theaters/' => 'theaters/',
            '../data/img/food/' => 'food/',
            '../data/phim/phimle/' => 'movies/phimle/',
            '../data/phim/phimbo/' => 'movies/phimbo/',
            '../data/phim/trailers/' => 'movies/trailers/',
        ];
        
        foreach ($replacements as $old => $new) {
            if (str_starts_with($oldPath, $old)) {
                return str_replace($old, $new, $oldPath);
            }
        }
        
        return $oldPath;
    }
}

if (!function_exists('storage_url')) {
    /**
     * Get full URL for storage file
     * Automatically converts old paths to new paths
     * 
     * @param string|null $path
     * @return string|null
     */
    function storage_url(?string $path): ?string
    {
        if (empty($path)) return null;
        
        // If path already starts with http, return as is
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        
        // Normalize and convert legacy paths
        $path = old_to_new_path($path);
        $path = ltrim($path, '/');

        // If path already points to the public storage link, use it directly
        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        // If file exists directly in public/, use direct asset URL
        if (file_exists(public_path($path))) {
            return asset($path);
        }

        // If file exists in public/storage/, use storage asset path
        if (file_exists(public_path('storage/' . $path))) {
            return asset('storage/' . $path);
        }

        // If file is available through Laravel public storage disk, use storage URL
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }

        // If the requested path is missing, use fallback placeholder image.
        if (file_exists(public_path('data/img/placeholder.svg'))) {
            return asset('data/img/placeholder.svg');
        }

        return asset('data/img/poster/poster_datve.jpg');
    }
}

if (!function_exists('storage_path_exists')) {
    /**
     * Check if file exists in storage
     * 
     * @param string|null $path
     * @return bool
     */
    function storage_path_exists(?string $path): bool
    {
        if (empty($path)) return false;
        
        $path = old_to_new_path($path);
        return \Illuminate\Support\Facades\Storage::disk('public')->exists($path);
    }
}

if (!function_exists('delete_storage_file')) {
    /**
     * Delete file from storage
     * 
     * @param string|null $path
     * @return bool
     */
    function delete_storage_file(?string $path): bool
    {
        if (empty($path)) return false;
        
        $path = old_to_new_path($path);
        
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
        }
        
        return false;
    }
}
