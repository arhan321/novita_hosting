// Multi Base Engineering Location
const COMPANY_LAT = -6.1754;
const COMPANY_LNG = 106.5772;
const COMPANY_ADDRESS = "Multi Base Engineering, Ruko Fiorenza, Jl. Raya H. Mirza Cinde Lakoni Jl. Citra Raya Boulevard, Ciakar, Kec. Panongan, Kabupaten Tangerang, Banten 15710";

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
    
    return Math.round(distance * 100) / 100; // Round to 2 decimal places
}

// Calculate shipping cost
function calculateShippingCost(method, distance, totalPrice) {
    if (method === 'pickup') {
        return 0;
    }
    
    if (method === 'internal') {
        // Jasa pribadi: minimal order Rp 500.000, maksimal 30 km
        if (totalPrice >= 500000 && distance <= 30) {
            return 0; // Gratis
        }
        return null; // Tidak memenuhi syarat
    }
    
    if (method === 'per_km') {
        return distance * 5000; // Rp 5.000 per km
    }
    
    return 0;
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

// Initialize shipping calculator
function initShippingCalculator(totalPrice = 0) {
    const shippingMethodSelect = document.getElementById('shipping_method');
    const addressGroup = document.getElementById('address_group');
    const shippingInfo = document.getElementById('shipping_info');
    const customerAddressInput = document.getElementById('customer_address');
    const latInput = document.getElementById('customer_latitude');
    const lngInput = document.getElementById('customer_longitude');
    
    if (!shippingMethodSelect) return;
    
    // Handle shipping method change
    shippingMethodSelect.addEventListener('change', function() {
        const method = this.value;
        
        if (method === 'pickup') {
            addressGroup.classList.add('hidden');
            updateShippingInfo(method, 0, 0, totalPrice);
        } else {
            addressGroup.classList.remove('hidden');
            if (latInput.value && lngInput.value) {
                const distance = calculateDistance(
                    COMPANY_LAT, COMPANY_LNG,
                    parseFloat(latInput.value), parseFloat(lngInput.value)
                );
                updateShippingInfo(method, distance, 0, totalPrice);
            }
        }
    });
    
    // Get current location
    window.getCurrentLocation = function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    latInput.value = lat;
                    lngInput.value = lng;
                    
                    // Reverse geocode to get address
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                        .then(response => response.json())
                        .then(data => {
                            customerAddressInput.value = data.display_name;
                            
                            const distance = calculateDistance(COMPANY_LAT, COMPANY_LNG, lat, lng);
                            const method = shippingMethodSelect.value;
                            updateShippingInfo(method, distance, 0, totalPrice);
                        })
                        .catch(error => {
                            console.error('Error getting address:', error);
                            alert('Gagal mendapatkan alamat. Silakan masukkan alamat secara manual.');
                        });
                },
                function(error) {
                    alert('Gagal mendapatkan lokasi: ' + error.message);
                }
            );
        } else {
            alert('Browser Anda tidak mendukung geolocation.');
        }
    };
    
    // Search address
    window.searchAddress = function() {
        const address = customerAddressInput.value;
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
                    
                    latInput.value = lat;
                    lngInput.value = lng;
                    
                    const distance = calculateDistance(COMPANY_LAT, COMPANY_LNG, lat, lng);
                    const method = shippingMethodSelect.value;
                    updateShippingInfo(method, distance, 0, totalPrice);
                } else {
                    alert('Alamat tidak ditemukan. Silakan coba alamat yang lebih spesifik.');
                }
            })
            .catch(error => {
                console.error('Error searching address:', error);
                alert('Gagal mencari alamat. Silakan coba lagi.');
            });
    };
    
    function updateShippingInfo(method, distance, cost, productPrice) {
        if (!shippingInfo) return;
        
        let html = '';
        
        if (method === 'pickup') {
            html = `
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        Anda akan mengambil pesanan di lokasi kami.
                    </p>
                    <p class="text-xs text-blue-600 mt-2">${COMPANY_ADDRESS}</p>
                    <p class="text-lg font-bold text-blue-900 mt-3">Biaya Pengiriman: Rp 0</p>
                </div>
            `;
        } else if (method === 'internal') {
            const shippingCost = calculateShippingCost(method, distance, productPrice);
            
            if (shippingCost === null) {
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
                            Total Pesanan: <strong>${formatCurrency(productPrice)}</strong>
                        </p>
                    </div>
                `;
            } else {
                html = `
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-sm text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>
                            Jasa pribadi tersedia untuk pesanan Anda!
                        </p>
                        <p class="text-xs text-green-600 mt-2">
                            Jarak: ${distance} km (maksimal 30 km)<br>
                            Total Pesanan: ${formatCurrency(productPrice)} (minimal Rp 500.000)
                        </p>
                        <p class="text-lg font-bold text-green-900 mt-3">Biaya Pengiriman: GRATIS</p>
                    </div>
                `;
            }
        } else if (method === 'per_km') {
            const shippingCost = calculateShippingCost(method, distance, productPrice);
            
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
                </div>
            `;
        }
        
        shippingInfo.innerHTML = html;
    }
}

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { initShippingCalculator, calculateDistance, calculateShippingCost, formatCurrency };
}
