<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('order.customer');

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $payments = $query->latest()->paginate(15);

        return view('admin.payments.index', compact('payments'));
    }

    public function verify(Request $request, Payment $payment)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            if ($request->action === 'approve') {
                $payment->update([
                    'status' => 'verified',
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);

                // Force fresh load from database
                $order = Order::find($payment->order_id);

                // Calculate total paid directly from database
                $totalPaid = Payment::where('order_id', $order->id)
                    ->where('status', 'verified')
                    ->sum('amount');

                $remaining = $order->total_price - $totalPaid;

                // Check if order is now fully paid
                if ($remaining <= 0) {
                    $order->update(['status' => 'paid']);
                    $message = 'Pembayaran diverifikasi. Pesanan sudah LUNAS.';
                } else {
                    // Ensure order stays as verified (not paid) for partial payments
                    if ($order->status === 'pending') {
                        $order->update(['status' => 'verified']);
                    }
                    $message = "Pembayaran diverifikasi. Sisa tagihan: Rp " . number_format($remaining, 0, ',', '.');
                }
            } else {
                $payment->update([
                    'status' => 'rejected',
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                    'rejection_reason' => $request->rejection_reason,
                ]);

                $message = 'Pembayaran ditolak. Customer perlu upload ulang.';
            }

            DB::commit();
            return redirect()->route('admin.payments.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal verifikasi pembayaran: ' . $e->getMessage());
        }
    }
}
