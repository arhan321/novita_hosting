@extends('layouts.app')

@section('title', 'Verifikasi Pembayaran')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Verifikasi Pembayaran</h1>
        <p class="mt-2 text-gray-600">Kelola dan verifikasi pembayaran customer</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-navy-800 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Payments List -->
    <div class="space-y-4">
        @forelse($payments as $payment)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $payment->order->order_number }}</h3>
                            <p class="text-sm text-gray-600 mt-1">Customer: {{ $payment->order->customer->name }}</p>
                            <p class="text-sm text-gray-600">{{ $payment->order->customer->email }}</p>
                        </div>
                        <div class="text-right">
                            @php
                                $statusClass = match($payment->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'verified' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-3 py-1 text-sm rounded-full {{ $statusClass }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                            <div class="text-2xl font-bold text-gray-900 mt-2">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </div>
                            <span class="text-xs px-2 py-1 rounded {{ $payment->payment_type === 'dp' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $payment->payment_type === 'dp' ? 'Down Payment' : ($payment->payment_type === 'full' ? 'Lunas' : 'Cicilan') }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 bg-gray-50 p-3 rounded">
                        <div>
                            <p class="text-xs text-gray-600">Total Tagihan</p>
                            <p class="text-sm font-bold text-gray-900">Rp {{ number_format($payment->order->total_price, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Total Dibayar</p>
                            <p class="text-sm font-bold text-green-600">Rp {{ number_format($payment->order->total_paid, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Sisa</p>
                            <p class="text-sm font-bold {{ $payment->order->remaining_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                Rp {{ number_format($payment->order->remaining_balance, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Metode Pembayaran</p>
                            <p class="text-sm font-medium text-gray-900">{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tanggal Upload</p>
                            <p class="text-sm font-medium text-gray-900">{{ $payment->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    @if($payment->notes)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600">Catatan</p>
                            <p class="text-sm text-gray-900">{{ $payment->notes }}</p>
                        </div>
                    @endif

                    <!-- Payment Proof -->
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Bukti Pembayaran</p>
                        @if($payment->payment_proof)
                            @php
                                $extension = pathinfo($payment->payment_proof, PATHINFO_EXTENSION);
                            @endphp
                            @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png']))
                                <a href="{{ route('files.payment-proofs.show', $payment) }}" target="_blank" rel="noopener noreferrer" class="inline-block">
                                    <img src="{{ route('files.payment-proofs.show', $payment) }}" alt="Bukti Pembayaran" class="max-w-md rounded-lg border">
                                </a>
                            @else
                                <a href="{{ route('files.payment-proofs.show', $payment) }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                    <i class="fas fa-file-pdf mr-2"></i>
                                    Lihat Bukti Pembayaran
                                </a>
                            @endif
                        @endif
                    </div>

                    <!-- Actions -->
                    @if($payment->status === 'pending')
                        <div class="flex space-x-2">
                            <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" class="inline">
                                @csrf
                                <input type="hidden" name="action" value="approve">
                                <button type="submit"
                                    class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition"
                                    onclick="return confirm('Verifikasi pembayaran ini?')">
                                    <i class="fas fa-check mr-2"></i>Verifikasi
                                </button>
                            </form>
                            <button type="button"
                                onclick="showRejectModal({{ $payment->id }})"
                                class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                                <i class="fas fa-times mr-2"></i>Tolak
                            </button>
                        </div>

                        <!-- Reject Modal Form -->
                        <div id="reject-modal-{{ $payment->id }}" class="hidden mt-4 p-4 bg-red-50 rounded-lg border border-red-200">
                            <form method="POST" action="{{ route('admin.payments.verify', $payment) }}">
                                @csrf
                                <input type="hidden" name="action" value="reject">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                                <textarea name="rejection_reason" rows="3" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 mb-2"
                                    placeholder="Jelaskan alasan penolakan (customer akan melihat ini)..."></textarea>
                                <div class="flex space-x-2">
                                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                                        Konfirmasi Tolak
                                    </button>
                                    <button type="button" onclick="hideRejectModal({{ $payment->id }})"
                                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-receipt text-gray-400 text-5xl mb-4"></i>
                <p class="text-gray-500">Tidak ada pembayaran</p>
            </div>
        @endforelse
    </div>

    @if($payments->hasPages())
        <div class="mt-6">
            {{ $payments->links() }}
        </div>
    @endif
</div>

<script>
function showRejectModal(paymentId) {
    document.getElementById('reject-modal-' + paymentId).classList.remove('hidden');
}
function hideRejectModal(paymentId) {
    document.getElementById('reject-modal-' + paymentId).classList.add('hidden');
}
</script>
@endsection
