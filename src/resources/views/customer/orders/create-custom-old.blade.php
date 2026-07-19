@extends('layouts.app')

@section('title', 'Pesan Custom Produk')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Form Pemesanan Custom</h1>
        <p class="text-gray-600 mb-6">Isi form berikut untuk membuat pesanan custom sesuai kebutuhan Anda</p>

        <form method="POST" action="{{ route('customer.orders.custom.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-6">
                <label for="product_name" class="block text-gray-700 text-sm font-bold mb-2">
                    Nama Produk
                    <span class="text-red-500">*</span>
                </label>
                <input type="text" name="product_name" id="product_name" value="{{ old('product_name') }}" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('product_name') border-red-500 @enderror"
                    placeholder="Contoh: Gate dengan Ornamen">
                @error('product_name')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="material" class="block text-gray-700 text-sm font-bold mb-2">
                        Bahan/Material
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="material" id="material" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('material') border-red-500 @enderror">
                        <option value="">Pilih Material</option>
                        <option value="Besi" {{ old('material') === 'Besi' ? 'selected' : '' }}>Besi</option>
                        <option value="Stainless Steel" {{ old('material') === 'Stainless Steel' ? 'selected' : '' }}>Stainless Steel</option>
                        <option value="Aluminium" {{ old('material') === 'Aluminium' ? 'selected' : '' }}>Aluminium</option>
                        <option value="Galvanis" {{ old('material') === 'Galvanis' ? 'selected' : '' }}>Galvanis</option>
                        <option value="Lainnya" {{ old('material') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('material')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">
                        Jumlah
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('quantity') border-red-500 @enderror">
                    @error('quantity')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="dimensions" class="block text-gray-700 text-sm font-bold mb-2">
                    Ukuran/Dimensi
                    <span class="text-red-500">*</span>
                </label>
                <input type="text" name="dimensions" id="dimensions" value="{{ old('dimensions') }}" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('dimensions') border-red-500 @enderror"
                    placeholder="Contoh: 2m x 1.5m x 0.5m">
                @error('dimensions')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">
                    Deskripsi Detail
                    <span class="text-red-500">*</span>
                </label>
                <textarea name="description" id="description" rows="5" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror"
                    placeholder="Jelaskan detail produk yang Anda inginkan...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="design_files" class="block text-gray-700 text-sm font-bold mb-2">
                    Upload Desain/Referensi (Opsional)
                </label>
                <input type="file" name="design_files[]" id="design_files" multiple accept=".pdf,.jpg,.jpeg,.png,.dwg,.dxf"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="mt-1 text-sm text-gray-500">Format: PDF, JPG, PNG, DWG, DXF. Max 10MB per file.</p>
                @error('design_files.*')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">
                    Catatan Tambahan (Opsional)
                </label>
                <textarea name="notes" id="notes" rows="3"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Catatan atau permintaan khusus...">{{ old('notes') }}</textarea>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-700">
                    <strong>Informasi:</strong> Untuk pesanan custom, harga akan ditentukan oleh admin setelah mereview spesifikasi Anda. Estimasi harga dan waktu pengerjaan akan diberitahukan melalui email.
                </p>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('customer.dashboard') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded">
                    Batal
                </a>
                <button type="submit"
                    class="bg-navy-800 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                    Kirim Pesanan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
