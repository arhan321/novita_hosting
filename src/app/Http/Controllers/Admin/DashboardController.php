<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'in_production' => Order::where('status', 'in_production')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
        ];

        $recent_orders = Order::with(['customer', 'items', 'payments'])
            ->latest()
            ->take(10)
            ->get();

        $pending_payments = Payment::with('order.customer')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_orders', 'pending_payments'));
    }
}
