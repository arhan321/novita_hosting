@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('admin.products.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Produk
        </a>
        <div class="space-x-2">
            <a href="{{ route('admin.products.edit', $product) }}"
                class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline-block">
                @csrf
                @method('DELETE')
                <button type="submit"
                    onclick="return confirm('Yakin hapus produk ini?')"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    <i class="fas fa-trash mr-2"></i>Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2">
            <!-- Product Image -->
            <div class="bg-gray-100 flex items-center justify-center p-8">
                @if($product->image_path)
                    <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}"
                        class="max-w-full max-h-96 object-contain rounded">
                @else
                    <div class="text-center text-gray-400">
                        <i class="fas fa-image text-6xl mb-4"></i>
                        <p>Tidak ada gambar</p>
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="p-8">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                    <div class="flex items-center space-x-3">
                        @if($product->is_available)
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                <i class="fas fa-check-circle mr-1"></i>Tersedia
                            </span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                                <i class="fas fa-times-circle mr-1"></i>Tidak Tersedia
                            </span>
                        @endif
                        @if($product->category)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                {{ $product->category }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="mb-6">
                    <p class="text-4xl font-bold text-blue-600">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </p>
                    <p class="text-sm text-gray-600 mt-1">per {{ $product->unit ?? 'unit' }}</p>
                </div>

                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Deskripsi</h3>
                    <p class="text-gray-600">{{ $product->description }}</p>
                </div>

                @if($product->material)
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Material</h3>
                        <p class="text-gray-600">{{ $product->material }}</p>
                    </div>
                @endif

                @if($product->specifications)
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Spesifikasi</h3>
                        <div class="bg-gray-50 rounded p-3">
                            @foreach($product->specifications as $key => $value)
                                <div class="flex justify-between py-1">
                                    <span class="text-sm text-gray-600">{{ ucfirst($key) }}:</span>
                                    <span class="text-sm font-medium">{{ $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t">
                    <div>
                        <p class="text-xs text-gray-600">Stok</p>
                        <p class="text-lg font-bold">{{ $product->stock ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Minimum Order</p>
                        <p class="text-lg font-bold">{{ $product->min_order ?? 1 }}</p>
                    </div>
                    @if($product->estimation_days)
                    <div class="col-span-2">
                        <p class="text-xs text-gray-600">Estimasi Pengerjaan</p>
                        <p class="text-lg font-bold text-orange-600">{{ $product->estimation_days }} Hari</p>
                    </div>
                    @endif
                </div>

                <div class="mt-6 pt-6 border-t">
                    <div class="text-xs text-gray-500">
                        <p>Dibuat: {{ $product->created_at->format('d M Y H:i') }}</p>
                        <p>Terakhir diupdate: {{ $product->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Orders (optional) -->
    @if($product->orderItems && $product->orderItems->count() > 0)
        <div class="mt-6 bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Riwayat Pesanan</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($product->orderItems->take(10) as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm">
                                    <a href="{{ route('admin.orders.show', $item->order) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $item->order->order_number }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $item->order->customer->name }}</td>
                                <td class="px-4 py-3 text-sm">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-sm">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm">{{ $item->created_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
