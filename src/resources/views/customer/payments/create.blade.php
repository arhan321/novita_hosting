@extends('layouts.app')

@section('title', 'Upload Bukti Pembayaran')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-4">
        <a href="{{ route('customer.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Detail Pesanan
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-navy-800 text-white">
            <h1 class="text-2xl font-bold">Upload Bukti Pembayaran</h1>
            <p class="text-sm mt-1">Pesanan: {{ $order->order_number }}</p>
        </div>

        <div class="p-6">
            <!-- Order Summary -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-gray-900 mb-3">Ringkasan Pesanan</h3>
                @php
                    $grandTotal = ($order->total_price ?? 0) + ($order->shipping_cost ?? 0);
                    $remainingBalance = $grandTotal - $order->total_paid;
                @endphp
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Nomor Pesanan</p>
                        <p class="font-medium">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Harga Produk</p>
                        <p class="font-medium text-gray-900">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </p>
                    </div>
                    @if($order->shipping_cost > 0)
                    <div>
                        <p class="text-sm text-gray-600">Biaya Pengiriman</p>
                        <p class="font-medium text-gray-900">
                            Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
                        </p>
                    </div>
                    @endif
                    <div class="{{ $order->shipping_cost > 0 ? '' : 'col-start-2' }}">
                        <p class="text-sm text-gray-600">Total Tagihan</p>
                        <p class="font-bold text-2xl text-orange-500">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </p>
                    </div>
                    @if($order->total_paid > 0)
                        <div>
                            <p class="text-sm text-gray-600">Sudah Dibayar</p>
                            <p class="font-bold text-lg text-green-600">
                                Rp {{ number_format($order->total_paid, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Sisa Tagihan</p>
                            <p class="font-bold text-2xl text-red-600">
                                Rp {{ number_format($remainingBalance, 0, ',', '.') }}
                            </p>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-600">Tipe Pesanan</p>
                        <p class="font-medium">{{ ucfirst($order->type) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Payment History (if any) -->
            @if($order->payments->isNotEmpty())
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <h3 class="text-sm font-semibold text-blue-800 mb-2">Riwayat Pembayaran</h3>
                    <div class="space-y-2">
                        @foreach($order->payments as $payment)
                            <div class="flex justify-between items-center text-sm">
                                <div>
                                    <span class="font-medium text-blue-900">
                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                    </span>
                                    <span class="text-blue-600 text-xs">
                                        ({{ ucfirst($payment->payment_type) }})
                                    </span>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full
                                    {{ $payment->status === 'verified' ? 'bg-green-100 text-green-800' :
                                       ($payment->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Payment Instructions -->
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Informasi Pembayaran</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p class="mb-2">Silakan lakukan transfer ke rekening berikut:</p>
                            <div class="bg-white p-3 rounded">
                                <p class="font-semibold">Bank Mandiri</p>
                                <p class="font-mono text-lg">1234-5678-9012-3456</p>
                                <p>a/n <strong>PT Multibase Engineering</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <form action="{{ route('customer.payments.store', $order) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Payment Method -->
                <div class="mb-6">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                        Metode Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <select id="payment_method" name="payment_method" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Metode Pembayaran</option>
                        <option value="bank_transfer">Transfer Bank</option>
                        <option value="cash">Cash / Tunai</option>
                        <option value="other">Lainnya</option>
                    </select>
                    @error('payment_method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Type -->
                <div class="mb-6">
                    <label for="payment_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <select id="payment_type" name="payment_type" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Tipe Pembayaran</option>
                        <option value="full">Lunas / Pelunasan</option>
                        <option value="dp">Down Payment (DP)</option>
                        <option value="installment">Cicilan</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">
                        @if($order->total_paid > 0)
                            Anda bisa melakukan pembayaran bertahap sesuai kesepakatan
                        @else
                            Pilih "DP" jika ingin bayar sebagian dulu, atau "Lunas" untuk bayar penuh
                        @endif
                    </p>
                    @error('payment_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div class="mb-6">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah Dibayar (Rp) <span class="text-red-500">*</span>
                    </label>
                    @php
                        $grandTotal = ($order->total_price ?? 0) + ($order->shipping_cost ?? 0);
                        $remainingBalance = $grandTotal - $order->total_paid;
                    @endphp
                    <input type="text" id="amount_display"
                        value="{{ number_format(old('amount', $remainingBalance), 0, ',', '.') }}" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <input type="hidden" id="amount" name="amount" value="{{ old('amount', $remainingBalance) }}">
                    <p class="mt-1 text-xs text-gray-500">
                        @if($order->total_paid > 0)
                            <span class="font-semibold text-red-600">Sisa tagihan: Rp {{ number_format($remainingBalance, 0, ',', '.') }}</span>
                            <br>Minimum: Rp 1.000 | Maksimum: Rp {{ number_format($remainingBalance, 0, ',', '.') }} (bisa input angka berapa saja)
                        @else
                            <span class="font-semibold text-orange-600">Total yang harus dibayar: Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                            @if($order->shipping_cost > 0)
                                <br><span class="text-gray-600">(Produk: Rp {{ number_format($order->total_price, 0, ',', '.') }} + Kirim: Rp {{ number_format($order->shipping_cost, 0, ',', '.') }})</span>
                            @endif
                            <br>Tip: Anda bisa bayar sebagian dulu (DP) atau langsung lunas (input nominal bebas)
                        @endif
                    </p>
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Proof -->
                <div class="mb-6">
                    <label for="payment_proof" class="block text-sm font-medium text-gray-700 mb-2">
                        Bukti Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <input type="file" id="payment_proof" name="payment_proof"
                        accept="image/*,.pdf" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Upload foto struk transfer atau bukti pembayaran (JPG, PNG, atau PDF, max 2MB)</p>
                    @error('payment_proof')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <!-- Preview -->
                    <div id="preview" class="mt-3 hidden">
                        <img id="preview-image" class="max-w-sm rounded border" alt="Preview">
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan (Opsional)
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Tambahkan catatan jika diperlukan...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('customer.orders.show', $order) }}"
                        class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-md shadow-md hover:shadow-lg transition duration-200">
                        <i class="fas fa-upload mr-2"></i>Upload Bukti Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Image preview
    document.getElementById('payment_proof').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').classList.remove('hidden');
                document.getElementById('preview-image').src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            document.getElementById('preview').classList.add('hidden');
        }
    });

    // Amount dot/thousand separator formatting
    const amountDisplay = document.getElementById('amount_display');
    const amountHidden = document.getElementById('amount');

    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    amountDisplay.addEventListener('input', function(e) {
        // Remove all non-digits
        let value = this.value.replace(/\D/g, '');
        
        // Update hidden field
        amountHidden.value = value;
        
        // Format display
        if (value) {
            this.value = formatNumber(value);
        } else {
            this.value = '';
        }
    });
</script>
@endpush
@endsection
