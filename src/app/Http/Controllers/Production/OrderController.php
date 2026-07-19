<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::whereIn('status', ['in_production', 'completed'])
            ->with(['customer', 'items.product', 'productionLogs' => function($q) {
                $q->latest();
            }]);

        // Filter by production stage if requested
        if ($request->has('stage') && $request->stage) {
            $stage = $request->stage;
            $query->whereHas('productionLogs', function($q) use ($stage) {
                // Get latest production log for each order
                $q->whereRaw('id IN (SELECT MAX(id) FROM production_logs GROUP BY order_id)')
                  ->where('stage', $stage);
            });
        }

        $orders = $query->latest()->paginate(15);

        return view('production.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if (!in_array($order->status, ['in_production', 'completed'])) {
            abort(403, 'Order tidak dalam status produksi.');
        }

        $order->load(['customer', 'items.product', 'files', 'productionLogs.updatedBy']);

        return view('production.orders.show', compact('order'));
    }

    public function updateProgress(Request $request, Order $order)
    {
        $request->validate([
            'stage' => 'required|in:pending,in_progress,finishing,completed',
            'notes' => 'required|string|max:1000',
        ]);

        try {
            $order->productionLogs()->create([
                'stage' => $request->stage,
                'notes' => $request->notes,
                'updated_by' => auth()->id(),
            ]);

            // If production completed, update order status
            if ($request->stage === 'completed') {
                $order->update([
                    'status' => 'completed',
                ]);

                return redirect()->route('production.orders.index')
                    ->with('success', 'Pesanan telah selesai diproduksi!');
            }

            return redirect()->route('production.orders.show', $order)
                ->with('success', 'Progress produksi berhasil diupdate.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update progress: ' . $e->getMessage());
        }
    }
}
