<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus host aplikasi dan prefix /storage dari data file lama.
     */
    public function up(): void
    {
        $fileColumns = [
            'products' => 'image_path',
            'order_files' => 'file_path',
            'payments' => 'payment_proof',
        ];

        foreach ($fileColumns as $table => $column) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
                continue;
            }

            DB::table($table)
                ->select('id', $column)
                ->whereNotNull($column)
                ->where($column, '!=', '')
                ->chunkById(200, function ($rows) use ($table, $column): void {
                    foreach ($rows as $row) {
                        $currentValue = $row->{$column};
                        $normalizedValue = $this->normalizeStoragePath($currentValue);

                        if ($normalizedValue !== $currentValue) {
                            DB::table($table)
                                ->where('id', $row->id)
                                ->update([$column => $normalizedValue]);
                        }
                    }
                });
        }
    }

    /**
     * Host deployment lama sengaja tidak dipasang kembali.
     */
    public function down(): void
    {
        // Tidak ada rollback karena path relatif adalah format yang portable.
    }

    private function normalizeStoragePath(string $value): string
    {
        $value = trim(str_replace('\\', '/', $value));

        if (preg_match('#^https?://#i', $value) === 1) {
            $urlPath = parse_url($value, PHP_URL_PATH);
            $storageMarker = '/storage/';

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
};
