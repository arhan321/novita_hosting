<?php

namespace App\Models;

use App\Support\StoragePath;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'material',
        'category',
        'specifications',
        'image_path',
        'is_active',
        'is_available',
        'stock',
        'min_order',
        'unit',
        'estimation_days',
    ];

    protected function casts(): array
    {
        return [
            'specifications' => 'array',
            'is_active' => 'boolean',
            'is_available' => 'boolean',
        ];
    }

    protected function imagePath(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => StoragePath::normalize($value),
            set: fn (?string $value) => StoragePath::normalize($value),
        );
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => StoragePath::publicUrl(
                $attributes['image_path'] ?? null
            ),
        );
    }

    // Relationships
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByMaterial($query, $material)
    {
        return $query->where('material', $material);
    }
}
