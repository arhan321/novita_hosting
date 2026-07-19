@extends('layouts.app')

@section('title', 'Knowledge Base Chat')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.chat.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-navy-800">
                    <i class="fas fa-book text-orange-500 mr-2"></i>Knowledge Base
                </h1>
                <p class="text-gray-500 text-sm mt-1">Kelola pertanyaan & jawaban otomatis bot</p>
            </div>
        </div>
        <button onclick="KB.openModal()"
            class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus mr-1"></i> Tambah Entri
        </button>
    </div>

    {{-- Confidence Threshold Setting --}}
    <div class="bg-white rounded-xl shadow p-5 mb-6">
        <h2 class="font-semibold text-gray-700 mb-3">
            <i class="fas fa-sliders-h text-orange-500 mr-1"></i> Pengaturan Bot
        </h2>
        <div class="flex items-center space-x-4">
            <label class="text-sm text-gray-600 font-medium">Confidence Threshold:</label>
            <input type="number" id="threshold-input"
                value="{{ $threshold }}"
                min="0.1" max="1.0" step="0.05"
                class="w-24 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            <button onclick="KB.saveThreshold()"
                class="bg-navy-700 hover:bg-navy-800 text-white text-sm px-4 py-1.5 rounded-lg transition">
                Simpan
            </button>
            <p class="text-xs text-gray-400">Nilai 0.1–1.0. Semakin tinggi = bot lebih ketat dalam menjawab.</p>
            <span id="threshold-msg" class="text-xs hidden"></span>
        </div>
    </div>

    {{-- Entries Table --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-gray-600 font-semibold w-1/4">Pertanyaan</th>
                    <th class="text-left px-5 py-3 text-gray-600 font-semibold">Jawaban</th>
                    <th class="text-left px-5 py-3 text-gray-600 font-semibold w-24">Kategori</th>
                    <th class="text-center px-5 py-3 text-gray-600 font-semibold w-20">Status</th>
                    <th class="text-center px-5 py-3 text-gray-600 font-semibold w-28">Aksi</th>
                </tr>
            </thead>
            <tbody id="kb-table-body">
                @forelse($entries as $entry)
                    <tr class="border-b border-gray-100 hover:bg-gray-50" id="kb-row-{{ $entry->id }}">
                        <td class="px-5 py-3 text-gray-800 font-medium align-top">{{ $entry->question }}</td>
                        <td class="px-5 py-3 text-gray-600 align-top">{{ Str::limit($entry->answer, 120) }}</td>
                        <td class="px-5 py-3 align-top">
                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">{{ $entry->category }}</span>
                        </td>
                        <td class="px-5 py-3 text-center align-top">
                            <button onclick="KB.toggle({{ $entry->id }}, this)"
                                class="text-xs px-2 py-1 rounded-full font-medium transition
                                    {{ $entry->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}"
                                data-active="{{ $entry->is_active ? '1' : '0' }}">
                                {{ $entry->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </td>
                        <td class="px-5 py-3 text-center align-top">
                            <button onclick="KB.openModal({{ $entry->id }}, {{ json_encode($entry->question) }}, {{ json_encode($entry->answer) }}, {{ json_encode($entry->category) }})"
                                class="text-navy-600 hover:text-navy-800 mr-2" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="KB.destroy({{ $entry->id }})"
                                class="text-red-500 hover:text-red-700" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 text-gray-400">
                            Belum ada entri knowledge base.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($entries->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $entries->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal --}}
<div id="kb-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 id="modal-title" class="font-bold text-gray-800">Tambah Entri</h3>
            <button onclick="KB.closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="px-6 py-4 space-y-4">
            <input type="hidden" id="modal-id">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pertanyaan <span class="text-red-500">*</span></label>
                <input type="text" id="modal-question" maxlength="500"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    placeholder="Contoh: Bagaimana cara memesan produk?">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jawaban <span class="text-red-500">*</span></label>
                <textarea id="modal-answer" rows="4" maxlength="2000"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"
                    placeholder="Tulis jawaban lengkap di sini..."></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                <input type="text" id="modal-category" maxlength="100" list="category-list"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    placeholder="order / pembayaran / produk / pengiriman / umum">
                <datalist id="category-list">
                    <option value="order">
                    <option value="pembayaran">
                    <option value="produk">
                    <option value="pengiriman">
                    <option value="umum">
                </datalist>
            </div>
            <p id="modal-error" class="text-xs text-red-500 hidden"></p>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
            <button onclick="KB.closeModal()"
                class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg transition">
                Batal
            </button>
            <button onclick="KB.save()"
                class="px-4 py-2 text-sm bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition">
                Simpan
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const KB = (() => {
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    async function apiFetch(url, options = {}) {
        const res = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
                ...options.headers,
            },
            ...options,
        });
        const data = await res.json();
        if (!res.ok) throw data;
        return data;
    }

    function openModal(id = null, question = '', answer = '', category = '') {
        document.getElementById('modal-id').value = id ?? '';
        document.getElementById('modal-question').value = question;
        document.getElementById('modal-answer').value = answer;
        document.getElementById('modal-category').value = category;
        document.getElementById('modal-title').textContent = id ? 'Edit Entri' : 'Tambah Entri';
        document.getElementById('modal-error').classList.add('hidden');
        document.getElementById('kb-modal').classList.remove('hidden');
        document.getElementById('modal-question').focus();
    }

    function closeModal() {
        document.getElementById('kb-modal').classList.add('hidden');
    }

    async function save() {
        const id = document.getElementById('modal-id').value;
        const question = document.getElementById('modal-question').value.trim();
        const answer = document.getElementById('modal-answer').value.trim();
        const category = document.getElementById('modal-category').value.trim();
        const errorEl = document.getElementById('modal-error');

        if (!question || !answer || !category) {
            errorEl.textContent = 'Semua field wajib diisi.';
            errorEl.classList.remove('hidden');
            return;
        }

        try {
            const url = id ? `/admin/chat/knowledge-base/${id}` : '/admin/chat/knowledge-base';
            const method = id ? 'PUT' : 'POST';
            await apiFetch(url, { method, body: JSON.stringify({ question, answer, category }) });
            closeModal();
            window.location.reload();
        } catch (e) {
            errorEl.textContent = e.message ?? 'Gagal menyimpan.';
            errorEl.classList.remove('hidden');
        }
    }

    async function destroy(id) {
        if (!confirm('Hapus entri ini?')) return;
        try {
            await apiFetch(`/admin/chat/knowledge-base/${id}`, { method: 'DELETE' });
            document.getElementById(`kb-row-${id}`)?.remove();
        } catch (e) {
            alert('Gagal menghapus entri.');
        }
    }

    async function toggle(id, btn) {
        try {
            const data = await apiFetch(`/admin/chat/knowledge-base/${id}/toggle`, { method: 'PATCH' });
            const isActive = data.is_active;
            btn.textContent = isActive ? 'Aktif' : 'Nonaktif';
            btn.className = `text-xs px-2 py-1 rounded-full font-medium transition ${isActive ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'}`;
            btn.dataset.active = isActive ? '1' : '0';
        } catch (e) {
            alert('Gagal mengubah status.');
        }
    }

    async function saveThreshold() {
        const val = parseFloat(document.getElementById('threshold-input').value);
        const msg = document.getElementById('threshold-msg');

        if (isNaN(val) || val < 0.1 || val > 1.0) {
            msg.textContent = 'Nilai harus antara 0.1 – 1.0';
            msg.className = 'text-xs text-red-500';
            msg.classList.remove('hidden');
            return;
        }

        try {
            await apiFetch('/admin/chat/settings/threshold', {
                method: 'POST',
                body: JSON.stringify({ threshold: val }),
            });
            msg.textContent = 'Tersimpan!';
            msg.className = 'text-xs text-green-600';
            msg.classList.remove('hidden');
            setTimeout(() => msg.classList.add('hidden'), 2000);
        } catch (e) {
            msg.textContent = 'Gagal menyimpan.';
            msg.className = 'text-xs text-red-500';
            msg.classList.remove('hidden');
        }
    }

    // Close modal on backdrop click
    document.getElementById('kb-modal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    return { openModal, closeModal, save, destroy, toggle, saveThreshold };
})();
</script>
@endpush
