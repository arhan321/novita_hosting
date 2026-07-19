<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        // Customer can only view their own orders
        if ($user->role === 'customer') {
            return $order->user_id === $user->id;
        }

        // Admin and production can view all orders
        return in_array($user->role, ['admin', 'production']);
    }

    public function update(User $user, Order $order): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->role === 'admin';
    }
}
