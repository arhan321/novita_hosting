<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'items.product', 'payments']);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Search by order number or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $orders = $query->latest()->paginate(20);
        
        return view('owner.invoices.index', compact('orders'));
    }
    
    public function show(Order $order)
    {
        $order->load(['customer', 'items.product', 'payments']);
        
        return view('owner.invoices.show', compact('order'));
    }
    
    public function downloadPdf(Order $order)
    {
        $order->load(['customer', 'items.product', 'payments']);
        
        $pdf = Pdf::loadView('owner.invoices.pdf', compact('order'));
        
        return $pdf->download("invoice_{$order->order_number}.pdf");
    }
    
    public function print(Order $order)
    {
        $order->load(['customer', 'items.product', 'payments']);
        
        return view('owner.invoices.print', compact('order'));
    }
}
