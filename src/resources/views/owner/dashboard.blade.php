@extends('layouts.app')

@section('title', 'Owner Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-navy-900">
            <i class="fas fa-chart-line text-orange-500 mr-2"></i>
            Dashboard Owner
        </h1>
        <p class="text-gray-600 mt-2">Monitoring bisnis dan keuangan</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-navy-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <i class="fas fa-money-bill-wave text-orange-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Pesanan</p>
                    <p class="text-2xl font-bold text-navy-900">{{ $totalOrders }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-shopping-cart text-blue-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Completed Orders -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pesanan Selesai</p>
                    <p class="text-2xl font-bold text-navy-900">{{ $completedOrders }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pembayaran Pending</p>
                    <p class="text-2xl font-bold text-navy-900">{{ $pendingPayments }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Monthly Revenue Line Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-navy-900 mb-4">
                <i class="fas fa-chart-line text-orange-500 mr-2"></i>
                Tren Pendapatan (6 Bulan Terakhir)
            </h2>
            <div style="height: 250px;">
                <canvas id="revenueLineChart"></canvas>
            </div>
        </div>

        <!-- Monthly Orders Bar Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-navy-900 mb-4">
                <i class="fas fa-chart-bar text-blue-500 mr-2"></i>
                Total Pesanan (6 Bulan Terakhir)
            </h2>
            <div style="height: 250px;">
                <canvas id="ordersBarChart"></canvas>
            </div>
        </div>
        
        <!-- Order Status Pie Chart -->
        <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-2">
            <h2 class="text-xl font-bold text-navy-900 mb-4">
                <i class="fas fa-chart-pie text-green-500 mr-2"></i>
                Distribusi Status Pesanan
            </h2>
            <div class="w-full md:w-1/2 mx-auto" style="height: 300px;">
                <canvas id="statusPieChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-navy-900 mb-4">
            <i class="fas fa-list text-orange-500 mr-2"></i>
            Pesanan Terbaru
        </h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentOrders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-navy-900">
                            {{ $order->order_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $order->customer->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order->created_at->format('d M Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada pesanan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data Setup
        const months = {!! json_encode($monthlyRevenue->pluck('month')->map(function($m) { return \Carbon\Carbon::parse($m)->format('M Y'); })) !!};
        const revenueData = {!! json_encode($monthlyRevenue->pluck('total')) !!};
        
        const orderMonths = {!! json_encode($monthlyOrders->pluck('month')->map(function($m) { return \Carbon\Carbon::parse($m)->format('M Y'); })) !!};
        const orderData = {!! json_encode($monthlyOrders->pluck('total')) !!};
        
        const statusLabels = {!! json_encode($ordersByStatus->pluck('status')->map(function($s) { return ucfirst(str_replace('_', ' ', $s)); })) !!};
        const statusData = {!! json_encode($ordersByStatus->pluck('count')) !!};

        // Revenue Line Chart
        const ctxLine = document.getElementById('revenueLineChart').getContext('2d');
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: revenueData,
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });

        // Orders Bar Chart
        const ctxBar = document.getElementById('ordersBarChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: orderMonths,
                datasets: [{
                    label: 'Jumlah Pesanan',
                    data: orderData,
                    backgroundColor: '#3b82f6',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        // Status Pie Chart
        const ctxPie = document.getElementById('statusPieChart').getContext('2d');
        const backgroundColors = statusLabels.map(label => {
            const lbl = label.toLowerCase();
            if(lbl === 'pending') return '#eab308';
            if(lbl === 'verified') return '#3b82f6';
            if(lbl === 'paid') return '#22c55e';
            if(lbl === 'in production') return '#a855f7';
            if(lbl === 'completed') return '#10b981';
            if(lbl === 'rejected') return '#ef4444';
            return '#64748b';
        });

        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: backgroundColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });
    });
</script>
@endpush
