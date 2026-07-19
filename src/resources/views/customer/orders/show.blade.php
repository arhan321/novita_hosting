@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-4">
        <a href="{{ route('customer.orders.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Pesanan
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Pesanan</h1>
                    <p class="text-sm text-gray-600 mt-1">No. Order: {{ $order->order_number }}</p>
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
                        $statusLabel = match($order->status) {
                            'pending' => 'Menunggu Verifikasi',
                            'verified' => 'Terverifikasi',
                            'paid' => 'Dibayar',
                            'in_production' => 'Dalam Produksi',
                            'completed' => 'Selesai',
                            'rejected' => 'Ditolak',
                            default => $order->status
                        };
                    @endphp
                    <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Order Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Informasi Pesanan</h3>
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Tipe Pesanan:</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                <span class="px-2 py-1 text-xs rounded-full {{ $order->type === 'katalog' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ ucfirst($order->type) }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Tanggal Pesanan:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $order->created_at->format('d M Y H:i') }}</dd>
                        </div>
                        @if($order->estimated_completion)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Estimasi Selesai:</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($order->estimated_completion)->format('d M Y') }}</dd>
                            </div>
                        @endif
                        @if($order->shipping_method)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Metode Pengiriman:</dt>
                                <dd class="text-sm font-medium text-gray-900">
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
                                </dd>
                            </div>
                        @endif
                        @if($order->customer_address && $order->shipping_method !== 'pickup')
                            <div class="col-span-2 mt-2">
                                <dt class="text-xs text-gray-600 mb-1">Alamat Pengiriman:</dt>
                                <dd class="text-xs text-gray-900 bg-gray-50 p-2 rounded">
                                    {{ $order->customer_address }}
                                    @if($order->distance_km)
                                        <br><span class="text-gray-600">Jarak: {{ $order->distance_km }} km</span>
                                    @endif
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Informasi Harga</h3>
                    <dl class="space-y-2">
                        @if($order->total_price)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Harga Produk:</dt>
                                <dd class="text-sm font-medium text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</dd>
                            </div>
                            @if($order->shipping_method && $order->shipping_method !== 'pickup')
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Biaya Pengiriman:</dt>
                                    <dd class="text-sm font-medium text-gray-900">Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}</dd>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-gray-200">
                                    <dt class="text-sm font-semibold text-gray-700">Total Keseluruhan:</dt>
                                    <dd class="text-lg font-bold text-orange-500">Rp {{ number_format(($order->total_price ?? 0) + ($order->shipping_cost ?? 0), 0, ',', '.') }}</dd>
                                </div>
                            @else
                                <div class="flex justify-between pt-2 border-t border-gray-200">
                                    <dt class="text-sm font-semibold text-gray-700">Total Keseluruhan:</dt>
                                    <dd class="text-lg font-bold text-orange-500">Rp {{ number_format($order->total_price, 0, ',', '.') }}</dd>
                                </div>
                            @endif
                        @else
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Total Harga:</dt>
                                <dd class="text-sm italic text-gray-500">Menunggu konfirmasi admin</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            @if($order->notes)
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Catatan</h3>
                    <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded">{{ $order->notes }}</p>
                </div>
            @endif

            <!-- Order Items -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Item</h3>
                <div class="border rounded-lg overflow-hidden">
                    @foreach($order->items as $item)
                        <div class="p-4 {{ !$loop->last ? 'border-b' : '' }}">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    @if($order->type === 'katalog' && $item->product)
                                        <h4 class="font-semibold text-gray-900">{{ $item->product->name }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ $item->product->description }}</p>
                                    @else
                                        <h4 class="font-semibold text-gray-900">{{ $item->product_name }}</h4>
                                    @endif

                                    @if($item->specifications)
                                        <div class="mt-3">
                                            <p class="text-xs font-semibold text-gray-700 mb-1">Spesifikasi:</p>
                                            <div class="bg-gray-50 rounded p-2">
                                                @foreach($item->specifications as $key => $value)
                                                    <div class="text-xs text-gray-600">
                                                        <span class="font-medium">{{ ucfirst($key) }}:</span> {{ $value }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4 text-right">
                                    <p class="text-sm text-gray-600">Jumlah: {{ $item->quantity }}</p>
                                    @if($item->unit_price)
                                        <p class="text-sm text-gray-600">@ Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                        <p class="text-lg font-bold text-gray-900 mt-1">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">File Desain</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($order->files->where('file_type', 'design') as $file)
                            <a href="{{ Storage::url($file->file_path) }}" target="_blank"
                               class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <i class="fas fa-file text-blue-600 text-2xl mr-3"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $file->file_name }}</p>
                                    <p class="text-xs text-gray-500">Klik untuk lihat</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Payment Summary -->
            @if($order->total_price)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-wallet text-blue-600 mr-2"></i>Ringkasan Pembayaran
                    </h3>
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-5 border border-blue-200">
                        @php
                            $grandTotal = ($order->total_price ?? 0) + ($order->shipping_cost ?? 0);
                        @endphp
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div>
                                <p class="text-xs font-medium text-gray-600 uppercase">Total Tagihan</p>
                                <p class="text-xl font-bold text-gray-900">
                                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                </p>
                                @if($order->shipping_cost > 0)
                                    <p class="text-xs text-gray-500 mt-1">
                                        (Produk: Rp {{ number_format($order->total_price, 0, ',', '.') }} + Kirim: Rp {{ number_format($order->shipping_cost, 0, ',', '.') }})
                                    </p>
                                @endif
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-600 uppercase">Sudah Dibayar</p>
                                <p class="text-xl font-bold text-green-600">
                                    Rp {{ number_format($order->total_paid, 0, ',', '.') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-600 uppercase">Sisa Tagihan</p>
                                @php
                                    $remainingBalance = $grandTotal - $order->total_paid;
                                @endphp
                                <p class="text-xl font-bold {{ $remainingBalance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    Rp {{ number_format($remainingBalance, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        @if($remainingBalance <= 0)
                            <div class="bg-green-100 border border-green-300 rounded-lg p-3 text-center">
                                <i class="fas fa-check-circle text-green-600 text-lg mr-2"></i>
                                <span class="text-green-800 font-semibold">Pembayaran LUNAS</span>
                            </div>
                        @elseif($order->total_paid > 0)
                            <div class="bg-yellow-100 border border-yellow-300 rounded-lg p-3 text-center">
                                <i class="fas fa-exclamation-circle text-yellow-600 text-lg mr-2"></i>
                                <span class="text-yellow-800 font-semibold">
                                    Pembayaran Parsial - Masih kurang Rp {{ number_format($remainingBalance, 0, ',', '.') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Payment History -->
            @if($order->payments->isNotEmpty())
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Pembayaran</h3>
                    <div class="space-y-3">
                        @foreach($order->payments as $index => $payment)
                            <div class="bg-white border rounded-lg p-4 {{ $payment->status === 'rejected' ? 'border-red-300 bg-red-50' : 'border-gray-200' }}">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="font-semibold text-gray-900">Pembayaran #{{ $index + 1 }}</span>
                                            <span class="px-2 py-1 text-xs rounded {{ $payment->payment_type === 'dp' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $payment->payment_type === 'dp' ? 'DP' : ($payment->payment_type === 'full' ? 'Lunas' : 'Cicilan') }}
                                            </span>
                                            @php
                                                $statusClass = match($payment->status) {
                                                    'verified' => 'bg-green-100 text-green-800',
                                                    'rejected' => 'bg-red-100 text-red-800',
                                                    default => 'bg-yellow-100 text-yellow-800'
                                                };
                                            @endphp
                                            <span class="px-2 py-1 text-xs rounded {{ $statusClass }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </div>
                                        <div class="grid grid-cols-3 gap-3 text-sm">
                                            <div>
                                                <p class="text-xs text-gray-600">Jumlah</p>
                                                <p class="font-bold text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-600">Metode</p>
                                                <p class="text-gray-900">{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-600">Tanggal</p>
                                                <p class="text-gray-900">{{ $payment->created_at->format('d M Y H:i') }}</p>
                                            </div>
                                        </div>
                                        @if($payment->notes)
                                            <div class="mt-2 text-xs text-gray-600">
                                                <i class="fas fa-comment mr-1"></i>{{ $payment->notes }}
                                            </div>
                                        @endif
                                        @if($payment->rejection_reason)
                                            <div class="mt-2 text-sm text-red-700 bg-red-50 p-2 rounded">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                <strong>Ditolak:</strong> {{ $payment->rejection_reason }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Production Logs -->
            @if($order->productionLogs->isNotEmpty())
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Progress Produksi</h3>
                    <div class="space-y-3">
                        @foreach($order->productionLogs as $log)
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    @php
                                        $stageIcon = match($log->stage) {
                                            'pending' => 'fa-hourglass-half text-yellow-500',
                                            'in_progress' => 'fa-cogs text-blue-500',
                                            'finishing' => 'fa-paint-brush text-purple-500',
                                            'completed' => 'fa-check-circle text-green-500',
                                            default => 'fa-circle text-gray-500'
                                        };
                                    @endphp
                                    <i class="fas {{ $stageIcon }} text-xl"></i>
                                </div>
                                <div class="ml-4 flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $log->stage)) }}</p>
                                            <p class="text-sm text-gray-600 mt-1">{{ $log->notes }}</p>
                                        </div>
                                        <p class="text-xs text-gray-500">{{ $log->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                @if($order->status === 'verified' && $order->total_price)
                    @php
                        $grandTotal = ($order->total_price ?? 0) + ($order->shipping_cost ?? 0);
                        $remainingBalance = $grandTotal - $order->total_paid;
                    @endphp
                    @if($remainingBalance > 0 && !$order->hasPendingPayment())
                        <a href="{{ route('customer.payments.create', $order) }}"
                           class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg transition inline-flex items-center shadow-md hover:shadow-lg">
                            <i class="fas fa-credit-card mr-2"></i>
                            @if($order->total_paid > 0)
                                Lanjutkan Pembayaran (Sisa: Rp {{ number_format($remainingBalance, 0, ',', '.') }})
                            @else
                                Upload Bukti Pembayaran (Total: Rp {{ number_format($grandTotal, 0, ',', '.') }})
                            @endif
                        </a>
                    @elseif($order->hasPendingPayment())
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-clock mr-2"></i>
                                Pembayaran sedang menunggu verifikasi admin. Anda bisa lanjutkan pembayaran setelah pembayaran sebelumnya diverifikasi.
                            </p>
                        </div>
                    @endif
                @elseif($order->status === 'pending')
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            Pesanan Anda sedang menunggu verifikasi dari admin. Kami akan menghubungi Anda segera.
                        </p>
                    </div>
                @elseif($order->status === 'paid')
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            Pembayaran Anda sudah dikonfirmasi. Pesanan akan segera diproses.
                        </p>
                    </div>
                @elseif($order->status === 'in_production')
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <p class="text-sm text-purple-800">
                            <i class="fas fa-tools mr-2"></i>
                            Pesanan Anda sedang dalam proses produksi. Lihat progress di atas.
                        </p>
                    </div>
                @elseif($order->status === 'completed')
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-sm text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>
                            Pesanan Anda sudah selesai! 
                            @if($order->shipping_method === 'pickup')
                                Silakan koordinasi untuk pengambilan di lokasi kami.
                            @else
                                Pesanan akan segera dikirim ke alamat Anda.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
