@extends('layouts.app')

@section('title', 'Daftar Pesanan Saya')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-64">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Pesanan Saya</h1>
        <p class="mt-2 text-gray-600">Lihat semua pesanan Anda</p>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pesanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $order->order_number }}
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
                                        'rejected' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                    $statusLabel = match($order->status) {
                                        'pending' => 'Menunggu',
                                        'verified' => 'Terverifikasi',
                                        'paid' => 'Dibayar',
                                        'in_production' => 'Produksi',
                                        'completed' => 'Selesai',
                                        'rejected' => 'Ditolak',
                                        default => $order->status
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($order->total_price)
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                @else
                                    <span class="text-gray-400">Belum ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('customer.orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-box-open text-5xl mb-4"></i>
                                    <p class="text-gray-500">Belum ada pesanan</p>
                                    <div class="mt-4 space-x-2">
                                        <a href="{{ route('customer.products.index') }}"
                                           class="inline-block bg-navy-800 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                                            Lihat Katalog
                                        </a>
                                        <a href="{{ route('customer.orders.custom.create') }}"
                                           class="inline-block bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
                                            Pesan Custom
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
