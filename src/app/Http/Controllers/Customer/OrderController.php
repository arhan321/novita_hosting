<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderFile;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class OrderController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;
    // Show catalog order form
    public function createCatalog(Product $product)
    {
        return view('customer.orders.create-catalog', compact('product'));
    }

    // Store catalog order
    public function storeCatalog(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
            'shipping_method' => 'required|in:pickup,internal,per_km',
            'customer_address' => 'required_if:shipping_method,internal,per_km|nullable|string|max:500',
            'customer_latitude' => 'required_if:shipping_method,internal,per_km|nullable|numeric',
            'customer_longitude' => 'required_if:shipping_method,internal,per_km|nullable|numeric',
        ]);

        DB::beginTransaction();
        try {
            $totalPrice = $product->price * $request->quantity;
            $shippingCost = 0;
            $distanceKm = null;

            // Calculate shipping cost
            if ($request->shipping_method !== 'pickup') {
                $distanceKm = $this->calculateDistance(
                    $request->customer_latitude,
                    $request->customer_longitude
                );

                if ($request->shipping_method === 'internal') {
                    // Jasa pribadi: minimal order Rp 500.000, maksimal 30 km
                    if ($totalPrice >= 500000 && $distanceKm <= 30) {
                        $shippingCost = 0; // Gratis
                    } else {
                        return back()->with('error', 'Jasa pribadi hanya tersedia untuk pesanan minimal Rp 500.000 dan jarak maksimal 30 km.');
                    }
                } elseif ($request->shipping_method === 'per_km') {
                    $shippingCost = $distanceKm * 5000; // Rp 5.000 per km
                }
            }

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => $this->generateOrderNumber(),
                'type' => 'katalog',
                'status' => 'pending',
                'notes' => $request->notes,
                'total_price' => $totalPrice,
                'estimated_completion' => $product->estimation_days ? now()->addDays($product->estimation_days)->toDateString() : null,
                'shipping_method' => $request->shipping_method,
                'shipping_cost' => $shippingCost,
                'customer_address' => $request->customer_address,
                'distance_km' => $distanceKm,
                'customer_latitude' => $request->customer_latitude,
                'customer_longitude' => $request->customer_longitude,
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'unit_price' => $product->price,
                'subtotal' => $totalPrice,
                'specifications' => $product->specifications,
            ]);

            DB::commit();

            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Pesanan berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    // Show custom order form
    public function createCustom()
    {
        return view('customer.orders.create-custom');
    }

    // Store custom order
    public function storeCustom(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'material' => 'required|string|max:100',
            'dimensions' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'description' => 'required|string|max:2000',
            'notes' => 'nullable|string|max:1000',
            'design_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dwg,dxf|max:10240',
            'shipping_method' => 'required|in:pickup,internal,per_km',
            'customer_address' => 'required_if:shipping_method,internal,per_km|nullable|string|max:500',
            'customer_latitude' => 'required_if:shipping_method,internal,per_km|nullable|numeric',
            'customer_longitude' => 'required_if:shipping_method,internal,per_km|nullable|numeric',
        ]);

        DB::beginTransaction();
        try {
            $shippingCost = 0;
            $distanceKm = null;

            // Calculate shipping cost (price will be set by admin later)
            if ($request->shipping_method !== 'pickup') {
                $distanceKm = $this->calculateDistance(
                    $request->customer_latitude,
                    $request->customer_longitude
                );

                if ($request->shipping_method === 'per_km') {
                    $shippingCost = $distanceKm * 5000; // Rp 5.000 per km
                }
                // For internal, will be calculated after admin sets price
            }

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => $this->generateOrderNumber(),
                'type' => 'custom',
                'status' => 'pending',
                'notes' => $request->notes,
                'shipping_method' => $request->shipping_method,
                'shipping_cost' => $shippingCost,
                'customer_address' => $request->customer_address,
                'distance_km' => $distanceKm,
                'customer_latitude' => $request->customer_latitude,
                'customer_longitude' => $request->customer_longitude,
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_name' => $request->product_name,
                'quantity' => $request->quantity,
                'unit_price' => 0, // Price will be set by admin later
                'subtotal' => 0,
                'specifications' => [
                    'material' => $request->material,
                    'dimensions' => $request->dimensions,
                    'description' => $request->description,
                ],
            ]);

            // Upload design files
            if ($request->hasFile('design_files')) {
                foreach ($request->file('design_files') as $file) {
                    $path = $file->store('order-files', 'public');

                    OrderFile::create([
                        'order_id' => $order->id,
                        'file_type' => 'design',
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Pesanan custom berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    // Show order detail
    public function show(Order $order)
    {
        $this->authorize('view', $order);

        // Force fresh load from database
        $order = Order::with(['items.product', 'files', 'payments', 'productionLogs.updatedBy'])
            ->find($order->id);

        return view('customer.orders.show', compact('order'));
    }

    // List customer orders
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['items.product', 'payments'])
            ->latest()
            ->paginate(15);

        return view('customer.orders.index', compact('orders'));
    }

    private function generateOrderNumber()
    {
        $prefix = 'ORD';
        $date = date('Ymd');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        return $prefix . $date . $random;
    }

    /**
     * Calculate distance from Multi Base Engineering location
     * Location: -6.1754, 106.5772 (Citra Raya, Tangerang)
     */
    private function calculateDistance($lat2, $lon2)
    {
        $lat1 = -6.1754; // Multi Base Engineering latitude
        $lon1 = 106.5772; // Multi Base Engineering longitude

        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }
}
