@extends('layouts.app')

@section('title', 'Detail Pesanan - Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-4">
        <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Pesanan
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Header -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $order->order_number }}</h1>
                            <p class="text-sm text-gray-600 mt-1">
                                Customer: <span class="font-medium">{{ $order->customer->name }}</span>
                            </p>
                        </div>
                        <div>
                            @php
                                $statusClass = match($order->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'verified' => 'bg-blue-100 text-blue-800',
                                    'paid' => 'bg-indigo-100 text-indigo-800',
                                    'in_production' => 'bg-purple-100 text-purple-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $statusClass }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Order Info -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-600">Tipe Pesanan</p>
                            <p class="font-medium">
                                <span class="px-2 py-1 text-xs rounded-full {{ $order->type === 'katalog' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ ucfirst($order->type) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tanggal Pesanan</p>
                            <p class="font-medium">{{ $order->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email Customer</p>
                            <p class="font-medium">{{ $order->customer->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Telepon</p>
                            <p class="font-medium">{{ $order->customer->phone ?? '-' }}</p>
                        </div>
                        @if($order->shipping_method)
                        <div>
                            <p class="text-sm text-gray-600">Metode Pengiriman</p>
                            <p class="font-medium">
                                @if($order->shipping_method === 'pickup')
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                        <i class="fas fa-store mr-1"></i>Ambil Sendiri
                                    </span>
                                @elseif($order->shipping_method === 'internal')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-truck mr-1"></i>Jasa Pribadi
                                    </span>
                                @elseif($order->shipping_method === 'per_km')
                                    <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">
                                        <i class="fas fa-route mr-1"></i>Per Kilometer
                                    </span>
                                @endif
                            </p>
                        </div>
                        @endif
                        @if($order->distance_km && $order->shipping_method !== 'pickup')
                        <div>
                            <p class="text-sm text-gray-600">Jarak Pengiriman</p>
                            <p class="font-medium">{{ $order->distance_km }} km</p>
                        </div>
                        @endif
                    </div>

                    @if($order->customer_address && $order->shipping_method !== 'pickup')
                        <div class="mb-6">
                            <p class="text-sm text-gray-600 mb-1">Alamat Pengiriman</p>
                            <p class="text-sm bg-gray-50 p-3 rounded">{{ $order->customer_address }}</p>
                        </div>
                    @endif

                    @if($order->notes)
                        <div class="mb-6">
                            <p class="text-sm text-gray-600 mb-1">Catatan Customer</p>
                            <p class="text-sm bg-gray-50 p-3 rounded">{{ $order->notes }}</p>
                        </div>
                    @endif

                    <!-- Order Items -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Detail Item</h3>
                        <div class="border rounded-lg overflow-hidden">
                            @foreach($order->items as $item)
                                <div class="p-4 {{ !$loop->last ? 'border-b' : '' }}">
                                    <div class="flex justify-between">
                                        <div class="flex-1">
                                            @if($order->type === 'katalog' && $item->product)
                                                <h4 class="font-semibold">{{ $item->product->name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $item->product->description }}</p>
                                            @else
                                                <h4 class="font-semibold">{{ $item->product_name }}</h4>
                                            @endif

                                            @if($item->specifications)
                                                <div class="mt-2 bg-gray-50 rounded p-2">
                                                    <p class="text-xs font-semibold text-gray-700 mb-1">Spesifikasi:</p>
                                                    @foreach($item->specifications as $key => $value)
                                                        <div class="text-xs text-gray-600">
                                                            <span class="font-medium">{{ ucfirst($key) }}:</span> {{ $value }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4 text-right">
                                            <p class="text-sm">Qty: {{ $item->quantity }}</p>
                                            @if($item->unit_price)
                                                <p class="text-sm text-gray-600">@ Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                                <p class="font-bold text-lg">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Design Files -->
                    @if($order->files->isNotEmpty())
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">File Desain</h3>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($order->files->where('file_type', 'design') as $file)
                                    <a href="{{ route('files.order-files.show', $file) }}" target="_blank" rel="noopener noreferrer"
                                       class="flex items-center p-3 bg-gray-50 rounded hover:bg-gray-100">
                                        <i class="fas fa-file text-blue-600 text-xl mr-3"></i>
                                        <span class="text-sm truncate">{{ $file->file_name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Payment Info -->
                    @if($order->payment)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Informasi Pembayaran</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="grid grid-cols-3 gap-4 mb-3">
                                    <div>
                                        <p class="text-xs text-gray-600">Metode</p>
                                        <p class="text-sm font-medium">{{ ucwords(str_replace('_', ' ', $order->payment->payment_method)) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Jumlah</p>
                                        <p class="text-sm font-medium">Rp {{ number_format($order->payment->amount, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Status</p>
                                        @php
                                            $paymentStatusClass = match($order->payment->status) {
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'verified' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span class="px-2 py-1 text-xs rounded-full {{ $paymentStatusClass }}">
                                            {{ ucfirst($order->payment->status) }}
                                        </span>
                                    </div>
                                </div>
                                @if($order->payment->payment_proof)
                                    <div>
                                        <p class="text-xs text-gray-600 mb-2">Bukti Pembayaran</p>
                                        <a href="{{ route('files.payment-proofs.show', $order->payment) }}" target="_blank" rel="noopener noreferrer"
                                           class="inline-block">
                                            @php
                                                $ext = pathinfo($order->payment->payment_proof, PATHINFO_EXTENSION);
                                            @endphp
                                            @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png']))
                                                <img src="{{ route('files.payment-proofs.show', $order->payment) }}"
                                                     alt="Bukti Pembayaran" class="max-w-sm rounded border">
                                            @else
                                                <span class="text-blue-600 hover:text-blue-800">
                                                    <i class="fas fa-file-pdf mr-1"></i>Lihat Bukti Pembayaran
                                                </span>
                                            @endif
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Production Logs -->
                    @if($order->productionLogs->isNotEmpty())
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Log Produksi</h3>
                            <div class="space-y-2">
                                @foreach($order->productionLogs as $log)
                                    <div class="flex items-start bg-gray-50 p-3 rounded">
                                        <i class="fas fa-circle text-xs text-gray-400 mt-1 mr-3"></i>
                                        <div class="flex-1">
                                            <div class="flex justify-between">
                                                <span class="font-medium text-sm">{{ ucfirst(str_replace('_', ' ', $log->stage)) }}</span>
                                                <span class="text-xs text-gray-500">{{ $log->created_at->format('d M Y H:i') }}</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">{{ $log->notes }}</p>
                                            <p class="text-xs text-gray-500 mt-1">By: {{ $log->updatedBy->name }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Update Status -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Update Status</h3>
                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="verified" {{ $order->status === 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="in_production" {{ $order->status === 'in_production' ? 'selected' : '' }}>In Production</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="rejected" {{ $order->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-navy-800 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Update Status
                    </button>
                </form>
            </div>

            <!-- Set Harga & Estimasi (untuk custom atau jika belum ada harga) -->
            @if($order->type === 'custom' || !$order->total_price)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Set Harga & Estimasi</h3>
                    <form method="POST" action="{{ route('admin.orders.update-price', $order) }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Harga (Rp)</label>
                            <input type="number" name="total_price" value="{{ $order->total_price }}" required min="0"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estimasi Selesai</label>
                            <input type="date" name="estimated_completion" value="{{ $order->estimated_completion }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            Simpan Harga
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Update Estimasi Selesai</h3>
                    <form method="POST" action="{{ route('admin.orders.update-price', $order) }}">
                        @csrf
                        <input type="hidden" name="total_price" value="{{ $order->total_price }}">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estimasi Selesai</label>
                            <input type="date" name="estimated_completion" value="{{ $order->estimated_completion }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            Simpan Estimasi
                        </button>
                    </form>
                </div>
            @endif

            <!-- Send to Production -->
            @if($order->status === 'paid')
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Kirim ke Produksi</h3>
                    <p class="text-sm text-gray-600 mb-4">Pesanan sudah dibayar dan siap dikirim ke tim produksi.</p>
                    <form method="POST" action="{{ route('admin.orders.send-to-production', $order) }}">
                        @csrf
                        <button type="submit" class="w-full bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700"
                            onclick="return confirm('Kirim pesanan ini ke produksi?')">
                            <i class="fas fa-paper-plane mr-2"></i>Kirim ke Produksi
                        </button>
                    </form>
                </div>
            @endif

            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Ringkasan</h3>
                <dl class="space-y-2">
                    @if($order->total_price)
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-600">Harga Produk:</dt>
                            <dd class="font-medium text-gray-900">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </dd>
                        </div>
                        @if($order->shipping_cost > 0)
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-600">Biaya Pengiriman:</dt>
                            <dd class="font-medium text-gray-900">
                                Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
                            </dd>
                        </div>
                        @endif
                        <div class="flex justify-between text-sm pt-2 border-t border-gray-200">
                            <dt class="text-gray-700 font-semibold">Total Keseluruhan:</dt>
                            <dd class="font-bold text-lg text-orange-500">
                                Rp {{ number_format(($order->total_price ?? 0) + ($order->shipping_cost ?? 0), 0, ',', '.') }}
                            </dd>
                        </div>
                    @else
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-600">Total Harga:</dt>
                            <dd class="font-bold text-gray-400">Belum diset</dd>
                        </div>
                    @endif
                    @if($order->estimated_completion)
                        <div class="flex justify-between text-sm pt-2">
                            <dt class="text-gray-600">Estimasi:</dt>
                            <dd class="font-medium">{{ \Carbon\Carbon::parse($order->estimated_completion)->format('d M Y') }}</dd>
                        </div>
                    @endif
                    @if($order->verified_at)
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-600">Diverifikasi:</dt>
                            <dd class="text-xs">{{ $order->verified_at->format('d M Y H:i') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
