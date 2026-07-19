<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function financial(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        // Total income (verified payments)
        $totalIncome = Payment::where('status', 'verified')
            ->whereBetween('verified_at', [$startDate, $endDate])
            ->sum('amount');
        
        // Orders summary
        $ordersCount = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedOrders = Order::where('status', 'completed')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();
        
        // Payment details
        $payments = Payment::with(['order.customer'])
            ->where('status', 'verified')
            ->whereBetween('verified_at', [$startDate, $endDate])
            ->orderBy('verified_at', 'desc')
            ->get();
        
        // Daily income
        $dailyIncome = Payment::where('status', 'verified')
            ->whereBetween('verified_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(verified_at) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return view('owner.reports.financial', compact(
            'totalIncome',
            'ordersCount',
            'completedOrders',
            'payments',
            'dailyIncome',
            'startDate',
            'endDate'
        ));
    }
    
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        $payments = Payment::with(['order.customer'])
            ->where('status', 'verified')
            ->whereBetween('verified_at', [$startDate, $endDate])
            ->orderBy('verified_at', 'desc')
            ->get();
        
        $filename = "laporan_keuangan_{$startDate}_to_{$endDate}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['Tanggal', 'No. Order', 'Pelanggan', 'Jumlah', 'Metode']);
            
            // Data
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->verified_at->format('Y-m-d H:i'),
                    $payment->order->order_number,
                    $payment->order->customer->name,
                    number_format($payment->amount, 0, ',', '.'),
                    ucfirst($payment->payment_method),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
