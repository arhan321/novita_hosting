<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'sender_type',
        'body',
        'is_read_by_admin',
        'is_read_by_customer',
    ];

    protected function casts(): array
    {
        return [
            'is_read_by_admin' => 'boolean',
            'is_read_by_customer' => 'boolean',
        ];
    }

    // Relationships
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Helpers
    public function isFromCustomer(): bool
    {
        return $this->sender_type === 'customer';
    }

    public function isFromBot(): bool
    {
        return $this->sender_type === 'bot';
    }

    public function isFromAdmin(): bool
    {
        return $this->sender_type === 'admin';
    }

    public function isSystem(): bool
    {
        return $this->sender_type === 'system';
    }

    public function getSenderLabel(): string
    {
        return match ($this->sender_type) {
            'customer' => $this->sender?->name ?? 'Customer',
            'bot' => 'Bot',
            'admin' => $this->sender?->name ?? 'Admin',
            'system' => 'Sistem',
            default => 'Unknown',
        };
    }
}
