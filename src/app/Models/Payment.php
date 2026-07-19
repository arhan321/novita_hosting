<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_type',
        'amount',
        'payment_proof',
        'status',
        'verified_by',
        'verified_at',
        'notes',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'amount' => 'decimal:2',
        ];
    }

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
