<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total revenue (verified payments)
        $totalRevenue = Payment::where('status', 'verified')->sum('amount');
        
        // Total orders
        $totalOrders = Order::count();
        
        // Completed orders
        $completedOrders = Order::where('status', 'completed')->count();
        
        // Pending payments
        $pendingPayments = Payment::where('status', 'pending')->count();
        
        // Monthly revenue (last 6 months)
        $monthlyRevenue = Payment::where('status', 'verified')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        // Monthly orders (last 6 months)
        $monthlyOrders = Order::where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(id) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        // Orders by status
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
        
        // Recent orders
        $recentOrders = Order::with(['customer', 'payments'])
            ->latest()
            ->take(10)
            ->get();
        
        return view('owner.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'completedOrders',
            'pendingPayments',
            'monthlyRevenue',
            'monthlyOrders',
            'ordersByStatus',
            'recentOrders'
        ));
    }
}
