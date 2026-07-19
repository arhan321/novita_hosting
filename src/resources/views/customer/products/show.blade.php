@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-4">
        <a href="{{ route('customer.products.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Katalog
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8">
            <!-- Product Image -->
            <div>
                @if($product->image_path)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full rounded-lg">
                @else
                    <div class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-6xl"></i>
                    </div>
                @endif
            </div>

            <!-- Product Details -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>

                <div class="mb-6">
                    <span class="inline-block bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full mr-2">
                        {{ $product->category }}
                    </span>
                    <span class="inline-block bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full {{ $product->estimation_days ? 'mr-2' : '' }}">
                        {{ $product->material }}
                    </span>
                    @if($product->estimation_days)
                        <span class="inline-block bg-orange-100 text-orange-800 text-sm px-3 py-1 rounded-full">
                            <i class="fas fa-clock mr-1"></i>Estimasi: {{ $product->estimation_days }} Hari
                        </span>
                    @endif
                </div>

                <div class="mb-6">
                    <p class="text-3xl font-bold text-blue-600">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </p>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Deskripsi</h3>
                    <p class="text-gray-600">{{ $product->description }}</p>
                </div>

                @if($product->specifications)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Spesifikasi</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            @foreach($product->specifications as $key => $value)
                                <div class="flex justify-between py-2 border-b border-gray-200 last:border-0">
                                    <span class="text-gray-600">{{ ucfirst($key) }}</span>
                                    <span class="text-gray-900 font-medium">{{ $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-8">
                    <a href="{{ route('customer.orders.catalog.create', $product) }}"
                       class="block w-full bg-navy-800 text-white text-center px-6 py-3 rounded-lg text-lg font-semibold hover:bg-blue-700 transition">
                        <i class="fas fa-shopping-cart mr-2"></i>Pesan Produk Ini
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
