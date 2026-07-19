<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'stage',
        'notes',
        'updated_by',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeByStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeLatest($query)
    {
        return $query->latest();
    }
}
