<?php

namespace App\Services;

use App\Support\StoragePath;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class FileUploadService
{
    /**
     * Upload file ke direktori tertentu pada public disk.
     */
    public function uploadPublic(UploadedFile $file, string $directory): string
    {
        $extension = strtolower($file->extension());
        $filename = Str::uuid().'.'.$extension;
        $path = $file->storeAs(trim($directory, '/'), $filename, 'public');

        if (!is_string($path) || $path === '') {
            throw new RuntimeException('File gagal disimpan ke public storage.');
        }

        if (!Storage::disk('public')->exists($path)) {
            throw new RuntimeException('File upload tidak ditemukan setelah disimpan.');
        }

        return StoragePath::normalize($path) ?? $path;
    }

    /**
     * Hapus file lokal dari public disk.
     */
    public function deletePublic(?string $filePath): bool
    {
        $path = StoragePath::normalize($filePath);

        if ($path === null || StoragePath::isUrl($path)) {
            return false;
        }

        return Storage::disk('public')->delete($path);
    }
}
