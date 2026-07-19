<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    public function create(Order $order)
    {
        $this->authorize('view', $order);

        // Check if order already fully paid
        if ($order->isFullyPaid()) {
            return redirect()->route('customer.orders.show', $order)
                ->with('info', 'Pesanan sudah lunas.');
        }

        // Check if there's pending payment
        if ($order->hasPendingPayment()) {
            return redirect()->route('customer.orders.show', $order)
                ->with('info', 'Masih ada pembayaran yang menunggu verifikasi admin.');
        }

        return view('customer.payments.create', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        $this->authorize('view', $order);

        $remainingBalance = $order->remaining_balance;

        $request->validate([
            'payment_method' => 'required|in:bank_transfer,cash,other',
            'payment_type' => 'required|in:full,dp,installment',
            'amount' => "required|numeric|min:1|max:{$remainingBalance}",
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $proofPath = $request->file('payment_proof')->store('payment-proofs', 'public');

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'payment_type' => $request->payment_type,
                'amount' => $request->amount,
                'payment_proof' => $proofPath,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            DB::commit();

            $message = 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.';

            // Calculate potential remaining balance after this payment is verified
            $potentialRemaining = $remainingBalance - $request->amount;
            if ($potentialRemaining > 0) {
                $message .= ' Sisa tagihan: Rp ' . number_format($potentialRemaining, 0, ',', '.');
            }

            return redirect()->route('customer.orders.show', $order)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal upload pembayaran: ' . $e->getMessage());
        }
    }
}
