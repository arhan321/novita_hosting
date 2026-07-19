@extends('layouts.app')

@section('title', 'Production Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Produksi</h1>
        <p class="mt-2 text-gray-600">Monitoring pesanan produksi</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-yellow-50 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                    <i class="fas fa-hourglass-half text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-yellow-800">Pending</p>
                    <p class="text-2xl font-bold text-yellow-900">{{ $stats['pending_orders'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                    <i class="fas fa-cogs text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-blue-800">In Progress</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $stats['in_progress'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                    <i class="fas fa-paint-brush text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-purple-800">Finishing</p>
                    <p class="text-2xl font-bold text-purple-900">{{ $stats['finishing'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-green-800">Selesai Hari Ini</p>
                    <p class="text-2xl font-bold text-green-900">{{ $stats['completed_today'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Pesanan dalam Produksi</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pesanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        @php
                            $latestLog = $order->productionLogs->first();
                            $currentStage = $latestLog ? $latestLog->stage : 'pending';
                            $stageClass = match($currentStage) {
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'in_progress' => 'bg-blue-100 text-blue-800',
                                'finishing' => 'bg-purple-100 text-purple-800',
                                'completed' => 'bg-green-100 text-green-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $order->order_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $order->customer->name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($order->type === 'katalog' && $order->items->first()?->product)
                                    {{ $order->items->first()->product->name }}
                                @else
                                    {{ $order->items->first()->product_name ?? 'Custom Product' }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 text-xs rounded-full {{ $stageClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $currentStage)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @if($order->estimated_completion)
                                    {{ \Carbon\Carbon::parse($order->estimated_completion)->format('d M Y') }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('production.orders.show', $order) }}"
                                   class="text-blue-600 hover:text-blue-900">
                                    Update Progress
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada pesanan dalam produksi
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
