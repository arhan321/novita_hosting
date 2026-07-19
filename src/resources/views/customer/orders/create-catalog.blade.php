@extends('layouts.app')

@section('title', 'Pesan Produk - ' . $product->name)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-4">
        <a href="{{ route('customer.products.show', $product) }}" class="text-navy-700 hover:text-navy-900">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold text-navy-900 mb-6">
            <i class="fas fa-shopping-cart text-orange-500 mr-2"></i>
            Form Pemesanan Katalog
        </h1>

        <!-- Product Summary -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-200">
            <div class="flex items-center">
                @if($product->image_path)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-20 h-20 object-cover rounded-lg mr-4">
                @endif
                <div>
                    <h3 class="font-semibold text-navy-900">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-600">{{ $product->material }} - {{ $product->category }}</p>
                    <p class="text-lg font-bold text-orange-500 mt-1" id="product-price" data-price="{{ $product->price }}">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('customer.orders.catalog.store', $product) }}" id="orderForm">
            @csrf

            <div class="mb-6">
                <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">
                    Jumlah
                    <span class="text-red-500">*</span>
                </label>
                <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" required
                    class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('quantity') border-red-500 @enderror"
                    onchange="updateShippingInfo()">
                @error('quantity')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Shipping Method -->
            <div class="mb-6">
                <label for="shipping_method" class="block text-gray-700 text-sm font-bold mb-2">
                    Metode Pengiriman
                    <span class="text-red-500">*</span>
                </label>
                <select name="shipping_method" id="shipping_method" required
                    class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('shipping_method') border-red-500 @enderror"
                    onchange="handleShippingMethodChange()">
                    <option value="">-- Pilih Metode Pengiriman --</option>
                    <option value="pickup" {{ old('shipping_method') == 'pickup' ? 'selected' : '' }}>Ambil Sendiri (Gratis)</option>
                    <option value="internal" {{ old('shipping_method') == 'internal' ? 'selected' : '' }}>Jasa Pribadi (Gratis - Syarat: Min. Rp 500K, Max. 30km)</option>
                    <option value="per_km" {{ old('shipping_method') == 'per_km' ? 'selected' : '' }}>Per Kilometer (Rp 5.000/km)</option>
                </select>
                @error('shipping_method')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address Group (Hidden by default) -->
            <div id="address_group" class="mb-6 hidden">
                <label for="customer_address" class="block text-gray-700 text-sm font-bold mb-2">
                    Alamat Pengiriman
                    <span class="text-red-500">*</span>
                </label>
                <textarea name="customer_address" id="customer_address" rows="3"
                    class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('customer_address') border-red-500 @enderror"
                    placeholder="Masukkan alamat lengkap Anda...">{{ old('customer_address') }}</textarea>
                @error('customer_address')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror

                <!-- Hidden fields for coordinates -->
                <input type="hidden" name="customer_latitude" id="customer_latitude" value="{{ old('customer_latitude') }}">
                <input type="hidden" name="customer_longitude" id="customer_longitude" value="{{ old('customer_longitude') }}">

                <!-- Location Buttons -->
                <div class="mt-3 flex gap-2">
                    <button type="button" onclick="getCurrentLocation()" class="bg-navy-700 hover:bg-navy-800 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 text-sm">
                        <i class="fas fa-map-marker-alt mr-2"></i>Gunakan Lokasi Saya
                    </button>
                    <button type="button" onclick="searchAddress()" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 text-sm">
                        <i class="fas fa-search mr-2"></i>Cari Alamat
                    </button>
                </div>
            </div>

            <!-- Shipping Info -->
            <div id="shipping_info" class="mb-6"></div>

            <div class="mb-6">
                <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">
                    Catatan Tambahan (Opsional)
                </label>
                <textarea name="notes" id="notes" rows="4"
                    class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    placeholder="Catatan atau permintaan khusus...">{{ old('notes') }}</textarea>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-700">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    @if($product->estimation_days)
                        <strong>Informasi:</strong> Estimasi pengerjaan untuk produk ini adalah <strong>{{ $product->estimation_days }} Hari</strong> setelah pesanan diverifikasi dan pembayaran dikonfirmasi.
                    @else
                        <strong>Informasi:</strong> Setelah pesanan dibuat, admin akan memverifikasi dan menentukan estimasi pengerjaan. Anda akan diberitahu melalui email.
                    @endif
                </p>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('customer.products.show', $product) }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-6 rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-check mr-2"></i>Buat Pesanan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Multi Base Engineering Location
const COMPANY_LAT = -6.1754;
const COMPANY_LNG = 106.5772;

// Calculate distance using Haversine formula
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Earth radius in km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    const distance = R * c;
    
    return Math.round(distance * 100) / 100;
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}

// Handle shipping method change
function handleShippingMethodChange() {
    const method = document.getElementById('shipping_method').value;
    const addressGroup = document.getElementById('address_group');
    
    if (method === 'pickup') {
        addressGroup.classList.add('hidden');
        updateShippingInfo();
    } else if (method === 'internal' || method === 'per_km') {
        addressGroup.classList.remove('hidden');
        const lat = document.getElementById('customer_latitude').value;
        const lng = document.getElementById('customer_longitude').value;
        if (lat && lng) {
            updateShippingInfo();
        }
    } else {
        addressGroup.classList.add('hidden');
        document.getElementById('shipping_info').innerHTML = '';
    }
}

// Get current location
function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                document.getElementById('customer_latitude').value = lat;
                document.getElementById('customer_longitude').value = lng;
                
                // Reverse geocode to get address
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('customer_address').value = data.display_name;
                        updateShippingInfo();
                    })
                    .catch(error => {
                        console.error('Error getting address:', error);
                        alert('Gagal mendapatkan alamat. Silakan masukkan alamat secara manual.');
                        updateShippingInfo();
                    });
            },
            function(error) {
                alert('Gagal mendapatkan lokasi: ' + error.message);
            }
        );
    } else {
        alert('Browser Anda tidak mendukung geolocation.');
    }
}

// Search address
function searchAddress() {
    const address = document.getElementById('customer_address').value;
    if (!address) {
        alert('Silakan masukkan alamat terlebih dahulu.');
        return;
    }
    
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);
                
                document.getElementById('customer_latitude').value = lat;
                document.getElementById('customer_longitude').value = lng;
                
                updateShippingInfo();
            } else {
                alert('Alamat tidak ditemukan. Silakan coba alamat yang lebih spesifik.');
            }
        })
        .catch(error => {
            console.error('Error searching address:', error);
            alert('Gagal mencari alamat. Silakan coba lagi.');
        });
}

// Update shipping info
function updateShippingInfo() {
    const method = document.getElementById('shipping_method').value;
    const shippingInfo = document.getElementById('shipping_info');
    const quantity = parseInt(document.getElementById('quantity').value) || 1;
    const pricePerUnit = parseFloat(document.getElementById('product-price').dataset.price);
    const totalPrice = quantity * pricePerUnit;
    
    if (!method) {
        shippingInfo.innerHTML = '';
        return;
    }
    
    let html = '';
    
    if (method === 'pickup') {
        html = `
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Anda akan mengambil pesanan di lokasi kami.
                </p>
                <p class="text-xs text-blue-600 mt-2">Multi Base Engineering, Ruko Fiorenza, Jl. Raya H. Mirza Cinde Lakoni Jl. Citra Raya Boulevard, Ciakar, Kec. Panongan, Kabupaten Tangerang, Banten 15710</p>
                <p class="text-lg font-bold text-blue-900 mt-3">Biaya Pengiriman: Rp 0</p>
                <p class="text-sm text-gray-700 mt-2">Total Pesanan: ${formatCurrency(totalPrice)}</p>
            </div>
        `;
    } else {
        const lat = parseFloat(document.getElementById('customer_latitude').value);
        const lng = parseFloat(document.getElementById('customer_longitude').value);
        
        if (!lat || !lng) {
            html = `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Silakan tentukan lokasi Anda terlebih dahulu.
                    </p>
                </div>
            `;
        } else {
            const distance = calculateDistance(COMPANY_LAT, COMPANY_LNG, lat, lng);
            
            if (method === 'internal') {
                if (totalPrice >= 500000 && distance <= 30) {
                    html = `
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-sm text-green-800">
                                <i class="fas fa-check-circle mr-2"></i>
                                Jasa pribadi tersedia untuk pesanan Anda!
                            </p>
                            <p class="text-xs text-green-600 mt-2">
                                Jarak: ${distance} km (maksimal 30 km)<br>
                                Total Pesanan: ${formatCurrency(totalPrice)} (minimal Rp 500.000)
                            </p>
                            <p class="text-lg font-bold text-green-900 mt-3">Biaya Pengiriman: GRATIS</p>
                        </div>
                    `;
                } else {
                    html = `
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <p class="text-sm text-red-800">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Jasa pribadi hanya tersedia untuk:
                            </p>
                            <ul class="text-xs text-red-600 mt-2 ml-6 list-disc">
                                <li>Pesanan minimal Rp 500.000</li>
                                <li>Jarak maksimal 30 km</li>
                            </ul>
                            <p class="text-sm text-red-800 mt-2">
                                Jarak Anda: <strong>${distance} km</strong><br>
                                Total Pesanan: <strong>${formatCurrency(totalPrice)}</strong>
                            </p>
                        </div>
                    `;
                }
            } else if (method === 'per_km') {
                const shippingCost = distance * 5000;
                html = `
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <p class="text-sm text-orange-800">
                            <i class="fas fa-truck mr-2"></i>
                            Pengiriman per kilometer
                        </p>
                        <p class="text-xs text-orange-600 mt-2">
                            Jarak: ${distance} km<br>
                            Tarif: Rp 5.000 per km
                        </p>
                        <p class="text-lg font-bold text-orange-900 mt-3">
                            Biaya Pengiriman: ${formatCurrency(shippingCost)}
                        </p>
                        <p class="text-sm text-gray-700 mt-2">
                            Total Pesanan: ${formatCurrency(totalPrice)}<br>
                            <strong>Grand Total: ${formatCurrency(totalPrice + shippingCost)}</strong>
                        </p>
                    </div>
                `;
            }
        }
    }
    
    shippingInfo.innerHTML = html;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const oldMethod = '{{ old("shipping_method") }}';
    if (oldMethod) {
        handleShippingMethodChange();
    }
});
</script>
@endsection
