@extends('layouts.app')

@section('title', 'Katalog Produk')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Katalog Produk</h1>
        <p class="mt-2 text-gray-600">Pilih produk dari katalog kami</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('customer.products.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari produk..."
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <select name="category" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                            {{ ucfirst($category) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="material" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Material</option>
                    @foreach($materials as $material)
                        <option value="{{ $material }}" {{ request('material') === $material ? 'selected' : '' }}>
                            {{ ucfirst($material) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="w-full bg-navy-800 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($products as $product)
            <div class="bg-slate-50 border border-slate-100 rounded-2xl shadow-sm hover:shadow-lg hover:scale-[1.02] transition-all duration-300 flex flex-col justify-between">
                <div>
                    <!-- Circular Image Container -->
                    <div class="flex justify-center pt-6">
                        <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-md bg-gray-100 flex items-center justify-center">
                            @if($product->image_path)
                                <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-image text-gray-400 text-3xl"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6 text-center">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ Str::limit($product->description, 80) }}</p>
                        
                        <!-- Badges -->
                        <div class="flex justify-center gap-2 mb-2">
                            <span class="px-2.5 py-0.5 bg-navy-100 text-navy-800 text-xs font-semibold rounded-full">
                                {{ $product->material }}
                            </span>
                            <span class="px-2.5 py-0.5 bg-orange-100 text-orange-800 text-xs font-semibold rounded-full">
                                {{ $product->category }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="px-6 pb-6 pt-4 border-t border-gray-200/60 flex items-center justify-between">
                    <span class="text-lg font-extrabold text-blue-600">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </span>
                    <a href="{{ route('customer.products.show', $product) }}" class="bg-navy-800 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-orange-500 transition-colors duration-200">
                        Detail
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-box-open text-gray-400 text-5xl mb-4"></i>
                <p class="text-gray-500">Tidak ada produk ditemukan</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
