<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductionLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'pending_orders' => Order::where('status', 'in_production')
                ->whereHas('productionLogs', function($q) {
                    $q->where('stage', 'pending');
                })->count(),
            'in_progress' => Order::where('status', 'in_production')
                ->whereHas('productionLogs', function($q) {
                    $q->where('stage', 'in_progress');
                })->count(),
            'finishing' => Order::where('status', 'in_production')
                ->whereHas('productionLogs', function($q) {
                    $q->where('stage', 'finishing');
                })->count(),
            'completed_today' => Order::where('status', 'completed')
                ->whereDate('updated_at', today())
                ->count(),
        ];

        $orders = Order::where('status', 'in_production')
            ->with(['customer', 'items.product', 'productionLogs' => function($q) {
                $q->latest();
            }])
            ->latest()
            ->paginate(15);

        return view('production.dashboard', compact('stats', 'orders'));
    }
}
