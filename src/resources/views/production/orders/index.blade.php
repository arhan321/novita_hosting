@extends('layouts.app')

@section('title', 'Daftar Pesanan Produksi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-48">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Daftar Pesanan Produksi</h1>
        <p class="text-gray-600 mt-1">Kelola dan update progress pesanan yang masuk ke produksi</p>
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

    <!-- Filter Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <a href="{{ route('production.orders.index') }}"
               class="{{ !request('stage') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Semua
                <span class="ml-2 py-0.5 px-2 rounded-full text-xs {{ !request('stage') ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-900' }}">
                    {{ $orders->total() }}
                </span>
            </a>
            <a href="{{ route('production.orders.index', ['stage' => 'pending']) }}"
               class="{{ request('stage') === 'pending' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Pending
            </a>
            <a href="{{ route('production.orders.index', ['stage' => 'in_progress']) }}"
               class="{{ request('stage') === 'in_progress' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                In Progress
            </a>
            <a href="{{ route('production.orders.index', ['stage' => 'finishing']) }}"
               class="{{ request('stage') === 'finishing' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Finishing
            </a>
            <a href="{{ route('production.orders.index', ['stage' => 'completed']) }}"
               class="{{ request('stage') === 'completed' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Completed
            </a>
        </nav>
    </div>

    <!-- Orders List -->
    @if($orders->isEmpty())
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Pesanan</h3>
            <p class="text-gray-500">Belum ada pesanan produksi {{ request('stage') ? 'dengan stage '.request('stage') : '' }}</p>
        </div>
    @else
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
                            Stage Produksi
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estimasi
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
                                <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                                <div class="text-xs text-gray-500">{{ $order->created_at->format('d M Y') }}</div>
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $latestLog = $order->productionLogs->first();
                                    $stage = $latestLog ? $latestLog->stage : 'pending';
                                    $stageClass = match($stage) {
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'in_progress' => 'bg-purple-100 text-purple-800',
                                        'finishing' => 'bg-indigo-100 text-indigo-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $stageClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $stage)) }}
                                </span>
                                @if($latestLog)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $latestLog->created_at->diffForHumans() }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($order->estimated_completion)
                                    {{ \Carbon\Carbon::parse($order->estimated_completion)->format('d M Y') }}
                                    <div class="text-xs {{ \Carbon\Carbon::parse($order->estimated_completion)->isPast() ? 'text-red-500' : 'text-gray-400' }}">
                                        {{ \Carbon\Carbon::parse($order->estimated_completion)->diffForHumans() }}
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('production.orders.show', $order) }}"
                                   class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </a>
                                @if($stage !== 'completed')
                                    <a href="{{ route('production.orders.show', $order) }}#update-progress"
                                       class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-edit mr-1"></i>Update
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
    @endif
</div>
@endsection
