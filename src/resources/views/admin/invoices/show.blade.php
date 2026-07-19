@extends('layouts.app')

@section('title', 'Detail Invoice')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-navy-900">
            <i class="fas fa-file-invoice text-orange-500 mr-2"></i>
            Invoice Detail
        </h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.invoices.pdf', $order) }}" 
                class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-file-pdf mr-2"></i>Download PDF
            </a>
            <a href="{{ route('admin.invoices.print', $order) }}" target="_blank"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-print mr-2"></i>Print
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <!-- Header -->
        <div class="border-b-2 border-navy-800 pb-6 mb-6">
            <div class="flex justify-between items-start">
                <div class="flex items-center">
                    <img src="{{ asset('img/logo.jpeg') }}" alt="Logo" class="h-16 w-auto mr-4 rounded-md">
                    <div>
                        <h2 class="text-2xl font-bold text-navy-900">Multi Base Engineering</h2>
                        <p class="text-sm text-gray-600 mt-2">
                            Ruko Fiorenza, Jl. Raya H. Mirza Cinde Lakoni<br>
                            Jl. Citra Raya Boulevard, Ciakar<br>
                            Kec. Panongan, Kabupaten Tangerang, Banten 15710
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <h3 class="text-xl font-bold text-orange-500">INVOICE</h3>
                    <p class="text-sm text-gray-600 mt-2">
                        <strong>No:</strong> {{ $order->order_number }}<br>
                        <strong>Tanggal:</strong> {{ $order->created_at->format('d F Y') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-2">KEPADA:</h4>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="font-semibold text-navy-900">{{ $order->customer->name }}</p>
                <p class="text-sm text-gray-600">{{ $order->customer->email }}</p>
                <p class="text-sm text-gray-600">{{ $order->customer->phone }}</p>
                @if($order->customer_address)
                <p class="text-sm text-gray-600 mt-2">{{ $order->customer_address }}</p>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">DETAIL PRODUK:</h4>
            <table class="w-full">
                <thead>
                    <tr class="bg-navy-800 text-white">
                        <th class="px-4 py-2 text-left">Produk</th>
                        <th class="px-4 py-2 text-center">Qty</th>
                        <th class="px-4 py-2 text-right">Harga</th>
                        <th class="px-4 py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($order->items as $item)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium text-navy-900">{{ $item->product_name }}</div>
                            @if($item->notes)
                            <div class="text-xs text-gray-500">{{ $item->notes }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Shipping Info -->
        @if($order->shipping_method && $order->shipping_method !== 'pickup')
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-2">PENGIRIMAN:</h4>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm">
                    <strong>Metode:</strong> 
                    @if($order->shipping_method === 'internal')
                        Jasa Pribadi (Internal)
                    @elseif($order->shipping_method === 'per_km')
                        Per Kilometer ({{ $order->distance_km }} km)
                    @endif
                </p>
            </div>
        </div>
        @endif

        <!-- Total -->
        <div class="border-t-2 border-gray-200 pt-4">
            <div class="flex justify-end">
                <div class="w-64">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-700">Subtotal:</span>
                        <span class="font-semibold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-700">Biaya Pengiriman:</span>
                        <span class="font-semibold">Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-navy-900 border-t-2 border-navy-800 pt-2">
                        <span>TOTAL:</span>
                        <span class="text-orange-500">Rp {{ number_format(($order->total_price ?? 0) + ($order->shipping_cost ?? 0), 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Status -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <h4 class="text-sm font-semibold text-gray-700">STATUS PEMBAYARAN:</h4>
                    @if($order->isFullyPaid())
                        <span class="inline-block mt-1 px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>LUNAS
                        </span>
                    @else
                        <span class="inline-block mt-1 px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1"></i>BELUM LUNAS
                        </span>
                    @endif
                </div>
                <div class="text-right">
                    <h4 class="text-sm font-semibold text-gray-700">STATUS PRODUKSI:</h4>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'verified' => 'bg-blue-100 text-blue-800',
                            'paid' => 'bg-green-100 text-green-800',
                            'in_production' => 'bg-purple-100 text-purple-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                        ];
                    @endphp
                    <span class="inline-block mt-1 px-4 py-2 rounded-full text-sm font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ strtoupper($order->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        @if($order->payments->count() > 0)
        <div class="mt-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">RIWAYAT PEMBAYARAN:</h4>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Tanggal</th>
                        <th class="px-4 py-2 text-left">Metode</th>
                        <th class="px-4 py-2 text-right">Jumlah</th>
                        <th class="px-4 py-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($order->payments as $payment)
                    <tr>
                        <td class="px-4 py-2">{{ $payment->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-2">{{ ucfirst($payment->payment_method) }}</td>
                        <td class="px-4 py-2 text-right font-semibold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-2 text-center">
                            @if($payment->status === 'verified')
                                <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-800">Verified</span>
                            @else
                                <span class="px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-800">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('admin.invoices.index') }}" class="text-navy-700 hover:text-navy-900">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Invoice
        </a>
    </div>
</div>
@endsection
