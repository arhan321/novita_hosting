<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $orders = Order::where('user_id', $user->id)
            ->with(['items.product', 'payments'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'pending_orders' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            'in_production' => Order::where('user_id', $user->id)->where('status', 'in_production')->count(),
            'completed_orders' => Order::where('user_id', $user->id)->where('status', 'completed')->count(),
        ];

        return view('customer.dashboard', compact('orders', 'stats'));
    }
}
