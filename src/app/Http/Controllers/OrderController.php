<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    // Dashboard - lihat semua pesanan
    public function index(): View
    {
        $orders = Order::with('customer', 'payment')
            ->latest()
            ->get();
        return view('admin.orders.index', compact('orders'));
    }

    // Detail pesanan
    public function show(Order $order): View
    {
        $order->load('customer', 'items', 'files', 'payment', 'productionLogs');
        return view('admin.orders.show', compact('order'));
    }

    // Verifikasi pesanan
    public function verify(Order $order)
    {
        // TODO: Verify order details & spesifikasi
    }

    // Reject pesanan
    public function reject(Order $order)
    {
        // TODO: Reject order dengan alasan
    }

    // Verify pembayaran
    public function verifyPayment(Order $order)
    {
        // TODO: Verify payment proof
    }

    // Reject pembayaran
    public function rejectPayment(Payment $payment)
    {
        // TODO: Reject payment dengan alasan
    }

    // Set harga final (untuk custom)
    public function setFinalPrice(Order $order)
    {
        // TODO: Set final price & estimasi waktu
    }

    // Send ke production
    public function sendToProduction(Order $order)
    {
        // TODO: Send order to production
    }

    // Update status produksi
    public function updateProductionStatus(Order $order)
    {
        // TODO: Update production status
    }

    // List pesanan pending verifikasi
    public function pendingVerification(): View
    {
        $orders = Order::byStatus('pending')
            ->latest()
            ->get();
        return view('admin.orders.pending-verification', compact('orders'));
    }

    // List pesanan pending pembayaran
    public function pendingPayment(): View
    {
        $orders = Order::byStatus('verified')
            ->with('payment')
            ->latest()
            ->get();
        return view('admin.orders.pending-payment', compact('orders'));
    }
}
