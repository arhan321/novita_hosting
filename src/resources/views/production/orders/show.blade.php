@extends('layouts.app')

@section('title', 'Detail Pesanan Produksi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-4">
        <a href="{{ route('production.orders.index') }}" class="text-blue-600 hover:text-blue-800">
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
                            <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $stageClass }}">
                                {{ ucfirst(str_replace('_', ' ', $stage)) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Order Info -->
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-600">Tipe Pesanan</p>
                            <p class="font-medium">
                                <span class="px-2 py-1 text-xs rounded-full {{ $order->type === 'katalog' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ ucfirst($order->type) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tanggal Masuk Produksi</p>
                            <p class="font-medium text-sm">{{ $order->created_at->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Estimasi Selesai</p>
                            <p class="font-medium text-sm">
                                @if($order->estimated_completion)
                                    {{ \Carbon\Carbon::parse($order->estimated_completion)->format('d M Y') }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($order->notes)
                        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-3">
                            <p class="text-xs font-semibold text-yellow-800 mb-1">Catatan dari Customer:</p>
                            <p class="text-sm text-yellow-900">{{ $order->notes }}</p>
                        </div>
                    @endif

                    <!-- Order Items -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Item yang Harus Diproduksi</h3>
                        <div class="border rounded-lg overflow-hidden">
                            @foreach($order->items as $item)
                                <div class="p-4 {{ !$loop->last ? 'border-b' : '' }} bg-white">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            @if($order->type === 'katalog' && $item->product)
                                                <h4 class="font-semibold text-lg">{{ $item->product->name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $item->product->description }}</p>
                                            @else
                                                <h4 class="font-semibold text-lg">{{ $item->product_name }}</h4>
                                            @endif

                                            @if($item->specifications)
                                                <div class="mt-3 bg-blue-50 rounded-lg p-3 border border-blue-200">
                                                    <p class="text-xs font-semibold text-blue-900 mb-2 uppercase">
                                                        <i class="fas fa-clipboard-list mr-1"></i>Spesifikasi Detail:
                                                    </p>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        @foreach($item->specifications as $key => $value)
                                                            <div class="text-sm">
                                                                <span class="font-semibold text-blue-800">{{ ucfirst($key) }}:</span>
                                                                <span class="text-gray-700">{{ $value }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4 text-right bg-gray-50 rounded-lg p-3">
                                            <p class="text-xs text-gray-600">Jumlah</p>
                                            <p class="text-2xl font-bold text-gray-900">{{ $item->quantity }}</p>
                                            <p class="text-xs text-gray-600">unit</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Design Files -->
                    @if($order->files->isNotEmpty())
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">
                                <i class="fas fa-file-image text-blue-600 mr-2"></i>File Desain
                            </h3>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($order->files->where('file_type', 'design') as $file)
                                    <a href="{{ route('files.order-files.show', $file) }}" target="_blank" rel="noopener noreferrer"
                                       class="flex items-center p-3 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
                                        <i class="fas fa-file-download text-blue-600 text-xl mr-3"></i>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-blue-900 truncate">{{ $file->file_name }}</p>
                                            <p class="text-xs text-blue-600">Klik untuk download</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Production Timeline -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">
                            <i class="fas fa-history text-purple-600 mr-2"></i>Timeline Produksi
                        </h3>
                        @if($order->productionLogs->isEmpty())
                            <p class="text-gray-500 text-sm bg-gray-50 p-4 rounded">Belum ada update progress</p>
                        @else
                            <div class="space-y-3">
                                @foreach($order->productionLogs as $log)
                                    <div class="flex items-start bg-gray-50 p-4 rounded-lg border-l-4
                                        {{ $log->stage === 'completed' ? 'border-green-500' :
                                           ($log->stage === 'finishing' ? 'border-indigo-500' :
                                           ($log->stage === 'in_progress' ? 'border-purple-500' : 'border-yellow-500')) }}">
                                        <div class="flex-shrink-0 mt-1">
                                            @php
                                                $icon = match($log->stage) {
                                                    'pending' => 'fa-clock',
                                                    'in_progress' => 'fa-hammer',
                                                    'finishing' => 'fa-paint-brush',
                                                    'completed' => 'fa-check-circle',
                                                    default => 'fa-circle'
                                                };
                                            @endphp
                                            <i class="fas {{ $icon }} text-lg
                                                {{ $log->stage === 'completed' ? 'text-green-600' :
                                                   ($log->stage === 'finishing' ? 'text-indigo-600' :
                                                   ($log->stage === 'in_progress' ? 'text-purple-600' : 'text-yellow-600')) }}"></i>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <span class="font-semibold text-gray-900">
                                                        {{ ucfirst(str_replace('_', ' ', $log->stage)) }}
                                                    </span>
                                                    <p class="text-sm text-gray-600 mt-1">{{ $log->notes }}</p>
                                                </div>
                                                <span class="text-xs text-gray-500 whitespace-nowrap ml-4">
                                                    {{ $log->created_at->format('d M Y H:i') }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-2">
                                                <i class="fas fa-user mr-1"></i>{{ $log->updatedBy->name }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Progress Sidebar -->
        <div class="lg:col-span-1">
            <div id="update-progress" class="bg-white rounded-lg shadow-lg p-6 sticky top-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-tasks text-blue-600 mr-2"></i>Update Progress
                </h3>

                @if($stage === 'completed')
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                        <i class="fas fa-check-circle text-green-600 text-4xl mb-2"></i>
                        <p class="text-green-800 font-semibold">Produksi Selesai</p>
                        <p class="text-xs text-green-600 mt-1">Pesanan ini sudah selesai diproduksi</p>
                    </div>
                @else
                    <form action="{{ route('production.orders.update-progress', $order) }}" method="POST">
                        @csrf

                        <!-- Current Stage Info -->
                        <div class="mb-4 bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-600 mb-1">Stage Saat Ini:</p>
                            <p class="font-semibold {{ $stageClass }} px-3 py-1 rounded inline-block text-sm">
                                {{ ucfirst(str_replace('_', ' ', $stage)) }}
                            </p>
                        </div>

                        <!-- New Stage Selection -->
                        <div class="mb-4">
                            <label for="stage" class="block text-sm font-medium text-gray-700 mb-2">
                                Update ke Stage <span class="text-red-500">*</span>
                            </label>
                            <select id="stage" name="stage" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Pilih Stage</option>
                                <option value="pending" {{ $stage === 'pending' ? 'selected' : '' }}>
                                    <i class="fas fa-clock"></i> Pending
                                </option>
                                <option value="in_progress" {{ $stage === 'in_progress' ? 'selected' : '' }}>
                                    🔨 In Progress
                                </option>
                                <option value="finishing" {{ $stage === 'finishing' ? 'selected' : '' }}>
                                    🎨 Finishing
                                </option>
                                <option value="completed" {{ $stage === 'completed' ? 'selected' : '' }}>
                                    ✅ Completed
                                </option>
                            </select>
                            @error('stage')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Progress Notes -->
                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan Progress <span class="text-red-500">*</span>
                            </label>
                            <textarea id="notes" name="notes" rows="4" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Jelaskan progress yang sudah dikerjakan..."></textarea>
                            <p class="mt-1 text-xs text-gray-500">Catatan akan terlihat di timeline</p>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full bg-navy-800 text-white px-4 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                            <i class="fas fa-save mr-2"></i>Simpan Update
                        </button>
                    </form>

                    <!-- Quick Info -->
                    <div class="mt-6 pt-6 border-t text-xs text-gray-500 space-y-2">
                        <p><i class="fas fa-info-circle text-blue-500 mr-1"></i> Update progress secara berkala</p>
                        <p><i class="fas fa-bell text-yellow-500 mr-1"></i> Customer akan melihat update Anda</p>
                    </div>
                @endif

                <!-- Order Summary -->
                <div class="mt-6 pt-6 border-t">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Info Pesanan</h4>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Customer:</dt>
                            <dd class="font-medium text-right">{{ $order->customer->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Email:</dt>
                            <dd class="font-medium text-right text-xs">{{ $order->customer->email }}</dd>
                        </div>
                        @if($order->estimated_completion)
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Deadline:</dt>
                                <dd class="font-medium text-right {{ \Carbon\Carbon::parse($order->estimated_completion)->isPast() ? 'text-red-600' : '' }}">
                                    {{ \Carbon\Carbon::parse($order->estimated_completion)->format('d M Y') }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
