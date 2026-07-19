<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'items.product', 'payments']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('customer', function($q2) use ($request) {
                      $q2->where('name', 'like', '%' . $request->search . '%')
                         ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $orders = $query->latest()->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items.product', 'files', 'payments', 'productionLogs.updatedBy']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,verified,paid,in_production,completed,rejected',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $order->status;

            $order->update([
                'status' => $request->status,
            ]);

            // If verifying order
            if ($request->status === 'verified' && $oldStatus === 'pending') {
                $order->update([
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);
            }

            DB::commit();

            // TODO: Send notification to customer

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Status pesanan berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update status: ' . $e->getMessage());
        }
    }

    public function updatePrice(Request $request, Order $order)
    {
        $request->validate([
            'total_price' => 'required|numeric|min:0',
            'estimated_completion' => 'nullable|date',
        ]);

        try {
            $order->update([
                'total_price' => $request->total_price,
                'estimated_completion' => $request->estimated_completion,
            ]);

            // Update order items with price (for custom orders)
            if ($order->type === 'custom' && $order->items->count() > 0) {
                foreach ($order->items as $item) {
                    $unitPrice = $request->total_price / ($item->quantity * $order->items->count());
                    $item->update([
                        'unit_price' => $unitPrice,
                        'subtotal' => $unitPrice * $item->quantity,
                    ]);
                }
            }

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Harga dan estimasi berhasil diupdate.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update data: ' . $e->getMessage());
        }
    }

    public function sendToProduction(Order $order)
    {
        if ($order->status !== 'paid') {
            return back()->with('error', 'Hanya pesanan yang sudah dibayar yang bisa dikirim ke produksi.');
        }

        try {
            $order->update([
                'status' => 'in_production',
            ]);

            // Create initial production log
            $order->productionLogs()->create([
                'stage' => 'pending',
                'notes' => 'Pesanan dikirim ke produksi',
                'updated_by' => auth()->id(),
            ]);

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Pesanan berhasil dikirim ke produksi.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal kirim ke produksi: ' . $e->getMessage());
        }
    }
}
