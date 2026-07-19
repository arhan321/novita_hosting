@extends('layouts.app')

@section('title', 'Pesanan Menunggu Pembayaran')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Pesanan Menunggu Pembayaran</h1>
        <p class="text-gray-600 mt-1">Pesanan yang sudah diverifikasi dan menunggu customer melakukan pembayaran</p>
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
            <i class="fas fa-wallet text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Pesanan Menunggu Pembayaran</h3>
            <p class="text-gray-500 mb-4">Semua pesanan sudah dibayar atau masih dalam proses verifikasi</p>
            <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Semua Pesanan
            </a>
        </div>
    @else
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Ada <strong>{{ $orders->total() }}</strong> pesanan yang sudah diverifikasi dan menunggu pembayaran dari customer.
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
                            Total
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status Pembayaran
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
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full">
                                                Verified
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $order->customer->name }}</div>
                                <div class="text-xs text-gray-500">{{ $order->customer->email }}</div>
                                @if($order->customer->phone)
                                    <div class="text-xs text-gray-500">
                                        <i class="fas fa-phone text-xs mr-1"></i>{{ $order->customer->phone }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $order->type === 'katalog' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ ucfirst($order->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($order->payment)
                                    @if($order->payment->status === 'pending')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>Menunggu Verifikasi
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Upload: {{ $order->payment->created_at->format('d M H:i') }}
                                        </div>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($order->payment->status) }}
                                        </span>
                                    @endif
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-circle mr-1"></i>Belum Upload
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->verified_at ? $order->verified_at->format('d M Y') : $order->created_at->format('d M Y') }}
                                <div class="text-xs text-gray-400">
                                    {{ $order->verified_at ? $order->verified_at->diffForHumans() : $order->created_at->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </a>
                                @if($order->payment && $order->payment->status === 'pending')
                                    <a href="{{ route('admin.payments.index') }}"
                                       class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-check-circle mr-1"></i>Verifikasi
                                    </a>
                                @endif
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

        <!-- Summary Stats -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <i class="fas fa-file-invoice-dollar text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Pesanan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $orders->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Menunggu Upload</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $orders->filter(fn($o) => !$o->payment)->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Nilai</p>
                        <p class="text-lg font-bold text-gray-900">
                            Rp {{ number_format($orders->sum('total_price'), 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
