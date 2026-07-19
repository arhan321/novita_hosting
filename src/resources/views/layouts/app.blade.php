<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Multi Base Engineering')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            50: '#f0f4f8',
                            100: '#d9e2ec',
                            200: '#bcccdc',
                            300: '#9fb3c8',
                            400: '#829ab1',
                            500: '#627d98',
                            600: '#486581',
                            700: '#334e68',
                            800: '#243b53',
                            900: '#102a43',
                        },
                        orange: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .btn-primary {
            @apply bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 shadow-md hover:shadow-lg;
        }
        .btn-secondary {
            @apply bg-navy-700 hover:bg-navy-800 text-white font-semibold py-2 px-6 rounded-lg transition duration-200;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-navy-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="text-xl font-bold text-white flex items-center">
                            <img src="{{ asset('img/logo.jpeg') }}" alt="Logo" class="h-8 w-auto mr-2 rounded-md">
                            Multi Base Engineering
                        </a>
                    </div>
                    @auth
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            @if(auth()->user()->role === 'customer')
                                <a href="{{ route('customer.dashboard') }}" class="border-transparent text-gray-300 hover:border-orange-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Dashboard
                                </a>
                                <a href="{{ route('customer.products.index') }}" class="border-transparent text-gray-300 hover:border-orange-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Katalog Produk
                                </a>
                                <a href="{{ route('customer.orders.custom.create') }}" class="border-transparent text-gray-300 hover:border-orange-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Custom Produk
                                </a>
                                <a href="{{ route('customer.orders.index') }}" class="border-transparent text-gray-300 hover:border-orange-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Pesanan Saya
                                </a>
                            @elseif(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="border-transparent text-gray-300 hover:border-orange-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Dashboard
                                </a>
                                <a href="{{ route('admin.orders.index') }}" class="border-transparent text-gray-300 hover:border-orange-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Pesanan
                                </a>
                                <a href="{{ route('admin.payments.index') }}" class="border-transparent text-gray-300 hover:border-orange-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Pembayaran
                                </a>
                                <a href="{{ route('admin.products.index') }}" class="border-transparent text-gray-300 hover:border-orange-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Produk
                                </a>
                                <a href="{{ route('admin.invoices.index') }}" class="border-transparent text-gray-300 hover:border-orange-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Invoice
                                </a>
                                <a href="{{ route('admin.chat.index') }}" class="border-transparent text-gray-300 hover:border-orange-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium relative">
                                    Chat
                                    @php
                                        $chatUnread = \App\Models\Conversation::active()
                                            ->whereHas('messages', fn($q) => $q->where('is_read_by_admin', false)->where('sender_type', 'customer'))
                                            ->count();
                                    @endphp
                                    @if($chatUnread > 0)
                                        <span class="ml-1 bg-red-500 text-white text-xs font-bold rounded-full px-1.5 py-0.5 leading-none">
                                            {{ $chatUnread > 99 ? '99+' : $chatUnread }}
                                        </span>
                                    @endif
                                </a>
                            @elseif(auth()->user()->role === 'production')
                                <a href="{{ route('production.dashboard') }}" class="border-transparent text-gray-300 hover:border-orange-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Dashboard
                                </a>
                                <a href="{{ route('production.orders.index') }}" class="border-transparent text-gray-300 hover:border-orange-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Pesanan Produksi
                                </a>
                            @elseif(auth()->user()->role === 'owner')
                                <a href="{{ route('owner.dashboard') }}" class="border-transparent text-gray-300 hover:border-orange-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Dashboard
                                </a>
                                <a href="{{ route('owner.reports.financial') }}" class="border-transparent text-gray-300 hover:border-orange-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Laporan Keuangan
                                </a>
                                <a href="{{ route('owner.invoices.index') }}" class="border-transparent text-gray-300 hover:border-orange-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Invoice
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        </div>
                    @endauth
                </div>
                <div class="flex items-center">
                    @guest
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-white px-3 py-2 text-sm font-medium">Login</a>
                        <a href="{{ route('register') }}" class="ml-4 bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-600 transition duration-200">Register</a>
                    @else
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-300">
                                <i class="fas fa-user-circle mr-1"></i>
                                {{ auth()->user()->name }}
                            </span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-gray-300 hover:text-white px-3 py-2 text-sm font-medium">
                                    <i class="fas fa-sign-out-alt mr-1"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('info') }}</span>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-navy-800 mt-0 border-t border-navy-700">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-400 text-sm">
                &copy; {{ date('Y') }} Multi Base Engineering. All rights reserved.
            </p>
        </div>
    </footer>

    @auth
        @if(auth()->user()->role === 'customer')
            @include('layouts.chat-widget')
        @endif
        @if(auth()->user()->role === 'admin')
            <script src="/js/admin-chat-notif.js"></script>
        @endif
    @endauth

    @stack('scripts')
</body>
</html>
