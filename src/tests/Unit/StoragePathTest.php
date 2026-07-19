<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Support\StoragePath;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StoragePathTest extends TestCase
{
    public function test_it_normalizes_legacy_public_storage_paths(): void
    {
        $this->assertSame(
            'products/example.jpg',
            StoragePath::normalize('/storage/products/example.jpg')
        );
        $this->assertSame(
            'products/example.jpg',
            StoragePath::normalize('public/products/example.jpg')
        );
        $this->assertSame(
            'products/example.jpg',
            StoragePath::normalize(
                'https://novita.djncloud.my.id/storage/products/example.jpg'
            )
        );
    }

    public function test_it_preserves_an_external_image_url(): void
    {
        $url = 'https://cdn.example.com/products/example.jpg';

        $this->assertSame($url, StoragePath::normalize($url));
        $this->assertSame($url, StoragePath::publicUrl($url));
    }

    public function test_product_exposes_a_host_independent_image_url(): void
    {
        Storage::fake('public');

        $product = new Product([
            'image_path' => 'https://novita.djncloud.my.id/storage/products/example.jpg',
        ]);

        $this->assertSame('products/example.jpg', $product->image_path);
        $this->assertSame('/storage/products/example.jpg', $product->image_url);
    }
}
