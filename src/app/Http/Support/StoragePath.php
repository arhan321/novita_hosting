<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

final class StoragePath
{
    /**
     * Simpan file lokal sebagai path relatif supaya tidak terikat domain.
     * Contoh hasil: products/nama-file.jpg.
     */
    public static function normalize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim(str_replace('\\', '/', $value));

        if ($value === '') {
            return null;
        }

        if (self::isUrl($value)) {
            $urlPath = parse_url($value, PHP_URL_PATH);
            $storageMarker = '/storage/';

            // URL eksternal yang bukan storage aplikasi tetap dipertahankan.
            if (!is_string($urlPath) || !str_contains($urlPath, $storageMarker)) {
                return $value;
            }

            $value = substr(
                $urlPath,
                strpos($urlPath, $storageMarker) + strlen($storageMarker)
            );
        }

        $value = preg_replace('#^/?(?:public/)?storage/#i', '', $value) ?? $value;
        $value = preg_replace('#^public/#i', '', $value) ?? $value;

        return ltrim($value, '/');
    }

    /**
     * Ubah path database menjadi URL browser yang tidak bergantung host.
     */
    public static function publicUrl(?string $value): ?string
    {
        $path = self::normalize($value);

        if ($path === null || self::isUrl($path)) {
            return $path;
        }

        $url = Storage::disk('public')->url($path);

        if (self::isUrl($url)) {
            $urlPath = parse_url($url, PHP_URL_PATH);

            return is_string($urlPath) ? '/'.ltrim($urlPath, '/') : $url;
        }

        return '/'.ltrim($url, '/');
    }

    public static function isUrl(?string $value): bool
    {
        return is_string($value)
            && preg_match('#^https?://#i', $value) === 1;
    }
}
