<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'type',
        'status',
        'notes',
        'total_price',
        'estimated_completion',
        'verified_by',
        'verified_at',
        'shipping_method',
        'shipping_cost',
        'customer_address',
        'distance_km',
        'customer_latitude',
        'customer_longitude',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'shipping_cost' => 'decimal:2',
            'distance_km' => 'decimal:2',
            'customer_latitude' => 'decimal:8',
            'customer_longitude' => 'decimal:8',
        ];
    }

    // Shipping helpers
    public function calculateShippingCost()
    {
        if ($this->shipping_method === 'pickup') {
            return 0;
        }
        
        if ($this->shipping_method === 'internal') {
            // Jasa pribadi: minimal order Rp 500.000, maksimal 30 km
            if ($this->total_price >= 500000 && $this->distance_km <= 30) {
                return 0; // Gratis
            }
            return null; // Tidak memenuhi syarat
        }
        
        if ($this->shipping_method === 'per_km' && $this->distance_km) {
            // Rp 5.000 per km
            return $this->distance_km * 5000;
        }
        
        return 0;
    }

    public function getTotalWithShippingAttribute()
    {
        return ($this->total_price ?? 0) + ($this->shipping_cost ?? 0);
    }

    // Payment helpers
    public function getTotalPaidAttribute()
    {
        // Force fresh query from database
        return \DB::table('payments')
            ->where('order_id', $this->id)
            ->where('status', 'verified')
            ->sum('amount');
    }

    public function getRemainingBalanceAttribute()
    {
        return max(0, ($this->total_price ?? 0) - $this->total_paid);
    }

    public function isFullyPaid()
    {
        if (!$this->total_price) {
            return false;
        }
        return $this->total_paid >= $this->total_price;
    }

    public function hasVerifiedPayment()
    {
        return \DB::table('payments')
            ->where('order_id', $this->id)
            ->where('status', 'verified')
            ->exists();
    }

    public function hasPendingPayment()
    {
        return \DB::table('payments')
            ->where('order_id', $this->id)
            ->where('status', 'pending')
            ->exists();
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function files()
    {
        return $this->hasMany(OrderFile::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function productionLogs()
    {
        return $this->hasMany(ProductionLog::class)->orderByDesc('created_at');
    }

    // Scopes
    public function scopeByCustomer($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helpers
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isVerified()
    {
        return $this->status === 'verified';
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isInProduction()
    {
        return $this->status === 'in_production';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
