@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-4">
        <a href="{{ route('admin.products.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-navy-800 text-white">
            <h1 class="text-2xl font-bold">Tambah Produk Baru</h1>
            <p class="text-sm mt-1">Lengkapi informasi produk yang akan ditambahkan</p>
        </div>

        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Product Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Produk <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select id="category" name="category" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih Kategori</option>
                            <option value="plat" {{ old('category') === 'plat' ? 'selected' : '' }}>Plat</option>
                            <option value="pipa" {{ old('category') === 'pipa' ? 'selected' : '' }}>Pipa</option>
                            <option value="besi_siku" {{ old('category') === 'besi_siku' ? 'selected' : '' }}>Besi Siku</option>
                            <option value="besi_hollow" {{ old('category') === 'besi_hollow' ? 'selected' : '' }}>Besi Hollow</option>
                            <option value="custom" {{ old('category') === 'custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Material -->
                    <div>
                        <label for="material" class="block text-sm font-medium text-gray-700 mb-2">
                            Material <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="material" name="material" value="{{ old('material') }}" required
                            placeholder="Contoh: Stainless Steel, Mild Steel, Aluminium"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('material')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="price" name="price" value="{{ old('price') }}"
                            required min="0" step="1000"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Unit -->
                    <div>
                        <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">
                            Satuan
                        </label>
                        <input type="text" id="unit" name="unit" value="{{ old('unit') }}"
                            placeholder="Contoh: pcs, kg, meter"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('unit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Stock -->
                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                            Stok
                        </label>
                        <input type="number" id="stock" name="stock" value="{{ old('stock', 0) }}"
                            min="0"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('stock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Min Order -->
                    <div>
                        <label for="min_order" class="block text-sm font-medium text-gray-700 mb-2">
                            Minimum Order
                        </label>
                        <input type="number" id="min_order" name="min_order" value="{{ old('min_order', 1) }}"
                            min="1"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('min_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estimation Days -->
                    <div>
                        <label for="estimation_days" class="block text-sm font-medium text-gray-700 mb-2">
                            Estimasi Pengerjaan (Hari)
                        </label>
                        <input type="number" id="estimation_days" name="estimation_days" value="{{ old('estimation_days') }}"
                            min="0"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Contoh: 7">
                        @error('estimation_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                            Gambar Produk
                        </label>
                        <input type="file" id="image" name="image" accept="image/*"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Upload gambar produk (JPG, PNG, max 2MB)</p>
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Availability -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Produk Tersedia</span>
                        </label>
                        @error('is_available')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Description (Full Width) -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" rows="4" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Deskripsi produk...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Specifications (Individual Fields) -->
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Spesifikasi Produk</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                    <!-- Ukuran/Dimensi -->
                    <div>
                        <label for="spec_ukuran" class="block text-sm font-medium text-gray-700 mb-2">
                            Ukuran/Dimensi
                        </label>
                        <input type="text" id="spec_ukuran" name="specifications[ukuran]" 
                            value="{{ old('specifications.ukuran') }}"
                            placeholder="Contoh: 100x50cm, 2m x 1m"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <!-- Ketebalan -->
                    <div>
                        <label for="spec_ketebalan" class="block text-sm font-medium text-gray-700 mb-2">
                            Ketebalan
                        </label>
                        <input type="text" id="spec_ketebalan" name="specifications[ketebalan]" 
                            value="{{ old('specifications.ketebalan') }}"
                            placeholder="Contoh: 2mm, 3mm"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <!-- Berat -->
                    <div>
                        <label for="spec_berat" class="block text-sm font-medium text-gray-700 mb-2">
                            Berat
                        </label>
                        <input type="text" id="spec_berat" name="specifications[berat]" 
                            value="{{ old('specifications.berat') }}"
                            placeholder="Contoh: 5kg, 10kg/meter"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <!-- Warna -->
                    <div>
                        <label for="spec_warna" class="block text-sm font-medium text-gray-700 mb-2">
                            Warna/Finishing
                        </label>
                        <input type="text" id="spec_warna" name="specifications[warna]" 
                            value="{{ old('specifications.warna') }}"
                            placeholder="Contoh: Silver, Hitam, Natural"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <!-- Grade/Kualitas -->
                    <div>
                        <label for="spec_grade" class="block text-sm font-medium text-gray-700 mb-2">
                            Grade/Kualitas
                        </label>
                        <input type="text" id="spec_grade" name="specifications[grade]" 
                            value="{{ old('specifications.grade') }}"
                            placeholder="Contoh: Grade A, SS304, SS316"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <!-- Standar -->
                    <div>
                        <label for="spec_standar" class="block text-sm font-medium text-gray-700 mb-2">
                            Standar
                        </label>
                        <input type="text" id="spec_standar" name="specifications[standar]" 
                            value="{{ old('specifications.standar') }}"
                            placeholder="Contoh: SNI, JIS, ASTM"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <!-- Panjang -->
                    <div>
                        <label for="spec_panjang" class="block text-sm font-medium text-gray-700 mb-2">
                            Panjang
                        </label>
                        <input type="text" id="spec_panjang" name="specifications[panjang]" 
                            value="{{ old('specifications.panjang') }}"
                            placeholder="Contoh: 6m, 12m"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <!-- Diameter -->
                    <div>
                        <label for="spec_diameter" class="block text-sm font-medium text-gray-700 mb-2">
                            Diameter
                        </label>
                        <input type="text" id="spec_diameter" name="specifications[diameter]" 
                            value="{{ old('specifications.diameter') }}"
                            placeholder="Contoh: 50mm, 2 inch"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <!-- Catatan Tambahan (Full Width) -->
                    <div class="md:col-span-2">
                        <label for="spec_catatan" class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan Tambahan
                        </label>
                        <textarea id="spec_catatan" name="specifications[catatan]" rows="2"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500"
                            placeholder="Informasi tambahan tentang spesifikasi produk...">{{ old('specifications.catatan') }}</textarea>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Isi field yang relevan dengan produk. Field yang kosong tidak akan disimpan.
                </p>
            </div>

            <!-- Submit Buttons -->
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.products.index') }}"
                    class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition duration-200">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-md shadow-md hover:shadow-lg transition duration-200">
                    <i class="fas fa-save mr-2"></i>Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
