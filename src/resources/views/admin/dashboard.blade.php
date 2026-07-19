@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Admin</h1>
        <p class="mt-2 text-gray-600">Monitoring sistem pemesanan</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">Total Pesanan</div>
            <div class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['total_orders'] }}</div>
        </div>
        <div class="bg-yellow-50 rounded-lg shadow p-6">
            <div class="text-sm font-medium text-yellow-800">Pending</div>
            <div class="text-2xl font-bold text-yellow-900 mt-2">{{ $stats['pending_orders'] }}</div>
        </div>
        <div class="bg-purple-50 rounded-lg shadow p-6">
            <div class="text-sm font-medium text-purple-800">Produksi</div>
            <div class="text-2xl font-bold text-purple-900 mt-2">{{ $stats['in_production'] }}</div>
        </div>
        <div class="bg-green-50 rounded-lg shadow p-6">
            <div class="text-sm font-medium text-green-800">Selesai</div>
            <div class="text-2xl font-bold text-green-900 mt-2">{{ $stats['completed_orders'] }}</div>
        </div>
        <div class="bg-blue-50 rounded-lg shadow p-6">
            <div class="text-sm font-medium text-blue-800">Total Customer</div>
            <div class="text-2xl font-bold text-blue-900 mt-2">{{ $stats['total_customers'] }}</div>
        </div>
        <div class="bg-red-50 rounded-lg shadow p-6">
            <div class="text-sm font-medium text-red-800">Pending Payment</div>
            <div class="text-2xl font-bold text-red-900 mt-2">{{ $stats['pending_payments'] }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Quick Actions -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
                <div class="space-y-2">
                    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
                       class="block w-full bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-4 py-3 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-clock mr-2"></i>Lihat Pesanan Pending
                    </a>
                    <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}"
                       class="block w-full bg-red-100 hover:bg-red-200 text-red-800 px-4 py-3 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-credit-card mr-2"></i>Verifikasi Pembayaran
                    </a>
                    <a href="{{ route('admin.products.create') }}"
                       class="block w-full bg-blue-100 hover:bg-blue-200 text-blue-800 px-4 py-3 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-plus mr-2"></i>Tambah Produk
                    </a>
                </div>
            </div>

            <!-- Pending Payments -->
            <div class="bg-white rounded-lg shadow p-6 mt-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Pending Payments</h2>
                <div class="space-y-3">
                    @forelse($pending_payments->take(5) as $payment)
                        <div class="border-l-4 border-yellow-500 bg-yellow-50 p-3 rounded">
                            <div class="text-sm font-medium text-gray-900">{{ $payment->order->order_number }}</div>
                            <div class="text-xs text-gray-600">{{ $payment->order->customer->name }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </div>
                            <a href="{{ route('admin.orders.show', $payment->order) }}"
                               class="text-xs text-blue-600 hover:text-blue-900 mt-1 inline-block">
                                Verifikasi →
                            </a>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">Tidak ada pembayaran pending</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Pesanan Terbaru</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Order</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recent_orders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $order->order_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $order->customer->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $order->type === 'katalog' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ ucfirst($order->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            $statusClass = match($order->status) {
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'verified' => 'bg-blue-100 text-blue-800',
                                                'paid' => 'bg-indigo-100 text-indigo-800',
                                                'in_production' => 'bg-purple-100 text-purple-800',
                                                'completed' => 'bg-green-100 text-green-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span class="px-2 py-1 text-xs rounded-full {{ $statusClass }}">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                           class="text-blue-600 hover:text-blue-900">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        Belum ada pesanan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
