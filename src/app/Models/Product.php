<?php

namespace App\Models;

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
