@extends('layouts.app')

@section('title', 'Multi base Engineering - Fabrikasi & Logam')

@section('content')
<!-- Hero Section -->
<div class="text-white" style="background-image: url('{{ asset('img/hero.jpeg') }}'); background-size: cover; background-position: center;">
    <div style="background: rgba(0,0,0,0.55);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-4">Multi base Engineering</h1>
                <p class="text-xl md:text-2xl mb-8">Rekayasa Teknik Presisi untuk Industri Modern</p>
                <p class="text-lg mb-8 max-w-2xl mx-auto">
                    Kami hadir sebagai mitra teknik terpercaya dengan komitmen terhadap kualitas
                    dan inovasi dari produk katalog hingga solusi custom sesuai kebutuhan industri Anda.
                </p>
                <div class="flex justify-center space-x-4">
                    @guest
                        <a href="{{ route('customer.products.index') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-100 transition">
                            Lihat Katalog
                        </a>
                        <a href="{{ route('login') }}" class="bg-blue-700 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-blue-800 transition">
                            Login
                        </a>
                    @else
                        @if(auth()->user()->role === 'customer')
                            <a href="{{ route('customer.products.index') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-100 transition">
                                Lihat Katalog
                            </a>
                            <a href="{{ route('customer.orders.custom.create') }}" class="bg-blue-700 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-blue-800 transition">
                                Pesan Custom
                            </a>
                        @elseif(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-100 transition">
                                Dashboard Admin
                            </a>
                        @else
                            <a href="{{ route('production.dashboard') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-100 transition">
                                Dashboard Produksi
                            </a>
                        @endif
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Layanan Kami</h2>
            <p class="text-lg text-gray-600">Kami menyediakan berbagai layanan fabrikasi dan logam</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-book text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Katalog Produk</h3>
                <p class="text-gray-600">Pilih dari berbagai produk ready di katalog kami. Pagar, kanopi, teralis, railing, dan lainnya.</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-pencil-ruler text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Custom Order</h3>
                <p class="text-gray-600">Buat pesanan custom sesuai spesifikasi Anda. Upload desain dan kami akan wujudkan.</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-tools text-purple-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Tracking Produksi</h3>
                <p class="text-gray-600">Pantau progress pengerjaan pesanan Anda secara real-time dari pending hingga selesai.</p>
            </div>
        </div>
    </div>
</div>

<!-- Location Section -->
<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Lokasi Kami</h2>
            <p class="text-lg text-gray-600">Kunjungi workshop kami atau hubungi untuk konsultasi</p>
        </div>
        <div class="rounded-lg overflow-hidden shadow-lg">
            <!-- <iframe 
                src="https://www.google.com/maps?q=-6.2088,106.8456&hl=id&z=15&output=embed" 
                width="100%" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
               referrerpolicy="no-referrer-when-downgrade">
            </iframe> -->
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.0224237008642!2d106.5307328!3d-6.2607764!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e42070039e99b51%3A0x600288cb7518efd4!2sMulti%20Base%20Engineering!5e0!3m2!1sid!2sid!4v1780421517596!5m2!1sid!2sid"
                width="100%"
                style="border:0;"
                height="450"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
            >
            </iframe>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-navy-800 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-4">Siap Memulai Proyek Anda?</h2>
        <p class="text-xl mb-8">Hubungi kami atau langsung buat pesanan online</p>
        @guest
            <a href="{{ route('register') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-100 transition inline-block">
                Daftar Sekarang
            </a>
        @else
            <a href="{{ route('customer.dashboard') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-100 transition inline-block">
                Ke Dashboard
            </a>
        @endguest
    </div>
</div>
@endsection
