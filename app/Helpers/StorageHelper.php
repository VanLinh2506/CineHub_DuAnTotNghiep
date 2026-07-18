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

if (!function_exists('normalize_storage_path')) {
    /**
     * Normalize a public disk path without losing compatibility with old values.
     *
     * @param string|null $path
     * @return string|null
     */
    function normalize_storage_path(?string $path): ?string
    {
        if (empty($path)) return null;

        $path = trim(str_replace('\\', '/', $path));
        $path = preg_replace('#^(\.\./)+#', '', $path);
        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        if (str_starts_with($path, 'data/storage/')) {
            $path = substr($path, strlen('data/storage/'));
        }

        return $path;
    }
}

if (!function_exists('storage_url')) {
    /**
     * Get full URL for storage file
     * Automatically converts old paths to new paths.
     * Handles both symlinked public/storage and real directory.
     * 
     * @param string|null $path
     * @return string|null
     */
    function storage_url(?string $path): ?string
    {
        if (empty($path)) return null;

        $path = trim($path);
        
        // If path already starts with http, return as is
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $normalizedPath = normalize_storage_path($path);
        $convertedPath = old_to_new_path($normalizedPath);

        // Determine the final relative path to use
        $finalPath = $normalizedPath;
        if (
            $convertedPath !== $normalizedPath &&
            !\Illuminate\Support\Facades\Storage::disk('public')->exists($normalizedPath) &&
            \Illuminate\Support\Facades\Storage::disk('public')->exists($convertedPath)
        ) {
            $finalPath = $convertedPath;
        }

        // Check if public/storage is a real directory (not a symlink)
        $publicStoragePath = public_path('storage');
        $isSymlinked = is_link($publicStoragePath);

        if ($isSymlinked) {
            // Keep local media relative to the current host. This prevents
            // APP_URL=127.0.0.1 from sending other devices to their own PC.
            return '/storage/' . ltrim($finalPath, '/');
        }

        // public/storage is a real directory - need to check if file exists there
        $publicFilePath = $publicStoragePath . '/' . ltrim($finalPath, '/');
        if (file_exists($publicFilePath)) {
            return '/storage/' . ltrim($finalPath, '/');
        }

        // File not in public/storage, it's in storage/app/public/
        // Use a host-relative URL so LAN/deployed clients use the same server.
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($finalPath)) {
            return '/storage/' . ltrim($finalPath, '/');
        }

        // Last resort: try old path location in public/storage/data/
        $oldPublicPath = $publicStoragePath . '/data/img/' . basename($finalPath);
        if (file_exists($oldPublicPath)) {
            return '/storage/data/img/' . basename($finalPath);
        }

        // Return null if nothing found
        return null;
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
        
        $path = normalize_storage_path($path);
        $convertedPath = old_to_new_path($path);

        return \Illuminate\Support\Facades\Storage::disk('public')->exists($path)
            || ($convertedPath !== $path && \Illuminate\Support\Facades\Storage::disk('public')->exists($convertedPath));
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
        
        $path = normalize_storage_path($path);
        $convertedPath = old_to_new_path($path);
        
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
        }

        if ($convertedPath !== $path && \Illuminate\Support\Facades\Storage::disk('public')->exists($convertedPath)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->delete($convertedPath);
        }
        
        return false;
    }
}
