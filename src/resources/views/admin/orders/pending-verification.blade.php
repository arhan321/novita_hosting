@extends('layouts.app')

@section('title', 'Pesanan Menunggu Verifikasi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Pesanan Menunggu Verifikasi</h1>
        <p class="text-gray-600 mt-1">Review dan verifikasi pesanan yang baru masuk</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($orders->isEmpty())
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <i class="fas fa-check-double text-green-300 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Semua Pesanan Sudah Terverifikasi</h3>
            <p class="text-gray-500 mb-4">Tidak ada pesanan yang menunggu verifikasi</p>
            <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Semua Pesanan
            </a>
        </div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Ada <strong>{{ $orders->total() }}</strong> pesanan yang menunggu verifikasi Anda.
                        Silakan review dan set harga untuk pesanan custom.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pesanan
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Customer
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipe
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Item
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total Harga
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                                        <div class="text-xs text-gray-500">
                                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded-full">
                                                Pending
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $order->customer->name }}</div>
                                <div class="text-xs text-gray-500">{{ $order->customer->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $order->type === 'katalog' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ ucfirst($order->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $order->items->count() }} item
                                </div>
                                @if($order->items->first())
                                    <div class="text-xs text-gray-500">
                                        {{ $order->items->first()->product_name ?? $order->items->first()->product->name ?? '-' }}
                                        @if($order->items->count() > 1)
                                            <span class="text-gray-400">+ {{ $order->items->count() - 1 }} lainnya</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($order->total_price)
                                    <div class="text-sm font-semibold text-gray-900">
                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                    </div>
                                @else
                                    <span class="text-xs text-red-600 font-semibold">
                                        <i class="fas fa-exclamation-circle mr-1"></i>Belum diset
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->format('d M Y') }}
                                <div class="text-xs text-gray-400">
                                    {{ $order->created_at->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-navy-800 text-white rounded hover:bg-blue-700">
                                    <i class="fas fa-eye mr-1"></i>Review & Verifikasi
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
