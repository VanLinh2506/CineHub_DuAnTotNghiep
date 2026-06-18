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
        
        // If path starts with data/img/ or data/phim/, these are in storage/app/public
        // so we need to prepend /storage/ to access them
        if (str_starts_with($path, 'data/img/') || str_starts_with($path, 'data/phim/')) {
            return asset('storage/' . $path);
        }
        
        // If path starts with data/storage/, remove the duplicate
        if (str_starts_with($path, 'data/storage/')) {
            $path = substr($path, strlen('data/storage/'));
            return asset('storage/' . $path);
        }
        
        // Convert old paths to new paths for compatibility
        $path = old_to_new_path($path);
        
        // Return full URL with storage prefix
        return asset('storage/' . ltrim($path, '/'));
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
