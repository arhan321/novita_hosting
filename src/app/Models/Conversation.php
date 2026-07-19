<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'customer_id',
        'mode',
        'handled_by',
        'taken_over_at',
        'taken_over_by_name',
        'handed_back_at',
        'handed_back_by_name',
        'is_active',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'taken_over_at' => 'datetime',
            'handed_back_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(Message::class)->latest()->limit(1);
    }

    // Helpers
    public function isBotMode(): bool
    {
        return $this->mode === 'bot';
    }

    public function isLiveMode(): bool
    {
        return $this->mode === 'live';
    }

    public function unreadCountForAdmin(): int
    {
        return $this->messages()->where('is_read_by_admin', false)
            ->whereIn('sender_type', ['customer'])
            ->count();
    }

    public function unreadCountForCustomer(): int
    {
        return $this->messages()->where('is_read_by_customer', false)
            ->whereIn('sender_type', ['bot', 'admin', 'system'])
            ->count();
    }

    public function markAdminRead(): void
    {
        $this->messages()
            ->where('is_read_by_admin', false)
            ->whereIn('sender_type', ['customer'])
            ->update(['is_read_by_admin' => true]);
    }

    public function markCustomerRead(): void
    {
        $this->messages()
            ->where('is_read_by_customer', false)
            ->whereIn('sender_type', ['bot', 'admin', 'system'])
            ->update(['is_read_by_customer' => true]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
}
