<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SupabaseStorage
{
    /**
     * Upload a file to Supabase and return the stored path.
     * Falls back to local public disk if Supabase is not configured.
     */
    public static function store(UploadedFile $file, string $folder): string
    {
        if (!self::isConfigured()) {
            return $file->store($folder, 'public');
        }

        $path = $file->store($folder, 'supabase');
        return $path;
    }

    /**
     * Delete a file from Supabase (and local fallback).
     */
    public static function delete(string $path): void
    {
        if (self::isConfigured()) {
            Storage::disk('supabase')->delete($path);
        }
        // Also try local in case it exists there
        Storage::disk('public')->delete($path);
    }

    /**
     * Get the public URL for a stored path.
     */
    public static function url(string $path): string
    {
        if (!$path) return '';

        if (self::isConfigured()) {
            return Storage::disk('supabase')->url($path);
        }

        return Storage::disk('public')->url($path);
    }

    /**
     * Check if Supabase is configured.
     */
    public static function isConfigured(): bool
    {
        return !empty(config('filesystems.disks.supabase.key'))
            && config('filesystems.disks.supabase.key') !== 'your-supabase-access-key-id';
    }
}
