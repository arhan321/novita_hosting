@extends('layouts.app')

@section('title', 'Chat - ' . $conversation->customer?->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Back + Header --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.chat.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-navy-800">
                    {{ $conversation->customer?->name ?? 'Unknown' }}
                </h1>
                <p class="text-sm text-gray-500">{{ $conversation->customer?->email }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            {{-- Notification bell for admin --}}
            <button data-chat-notif-btn
                onclick="AdminChatNotif.requestPermissionAndSubscribe()"
                title="Aktifkan notifikasi browser"
                class="hidden text-gray-400 hover:text-gray-600 border border-gray-200 rounded-lg px-3 py-2 text-sm transition"
                aria-label="Aktifkan notifikasi">
                <i class="fas fa-bell mr-1"></i> Notifikasi
            </button>
            {{-- Mute / unmute sound --}}
            <button data-chat-mute-btn
                onclick="AdminChatNotif.toggleMute()"
                title="Suara notifikasi aktif — klik untuk matikan"
                class="border border-gray-200 text-gray-500 hover:text-gray-700 px-3 py-2 rounded-lg transition"
                aria-label="Toggle suara notifikasi">
                <i class="fas fa-volume-up"></i>
            </button>
            {{-- Mode badge --}}
            <span id="mode-badge"
                class="text-sm px-3 py-1 rounded-full font-medium
                    {{ $conversation->mode === 'live' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                <i class="fas {{ $conversation->mode === 'live' ? 'fa-user-tie' : 'fa-robot' }} mr-1"></i>
                <span id="mode-label">{{ $conversation->mode === 'live' ? 'Live Mode' : 'Bot Mode' }}</span>
            </span>

            {{-- Takeover button --}}
            <button id="btn-takeover"
                onclick="AdminChat.takeover()"
                class="{{ $conversation->mode === 'bot' ? '' : 'hidden' }} bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                <i class="fas fa-hand-paper mr-1"></i> Ambil Alih
            </button>

            {{-- Handback button --}}
            <button id="btn-handback"
                onclick="AdminChat.handback()"
                class="{{ $conversation->mode === 'live' ? '' : 'hidden' }} bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                <i class="fas fa-robot mr-1"></i> Kembalikan ke Bot
            </button>
        </div>
    </div>

    {{-- Chat Box --}}
    <div class="bg-white rounded-xl shadow overflow-hidden flex flex-col" style="height: 560px;">

        {{-- Messages --}}
        <div id="chat-messages" class="flex-1 overflow-y-auto p-5 space-y-4 bg-gray-50">
            @foreach($messages as $msg)
                @include('admin.chat.partials.message', ['msg' => $msg])
            @endforeach

            {{-- Typing indicator --}}
            <div id="chat-typing" class="hidden">
                <div class="flex items-end space-x-2">
                    <div class="w-7 h-7 bg-gray-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-gray-500 text-xs"></i>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-2xl rounded-bl-none px-3 py-2 shadow-sm">
                        <div class="flex space-x-1">
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0s"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0.15s"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0.3s"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input (only in live mode) --}}
        <div id="admin-input-area"
            class="{{ $conversation->mode === 'live' ? '' : 'hidden' }} border-t border-gray-200 p-4 bg-white flex-shrink-0">
            <div class="flex items-end space-x-2">
                <textarea
                    id="admin-chat-input"
                    placeholder="Ketik balasan..."
                    rows="1"
                    maxlength="2000"
                    class="flex-1 resize-none border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 max-h-32 overflow-y-auto"
                    onkeydown="AdminChat.handleKeydown(event)"
                    oninput="AdminChat.autoResize(this)"
                    aria-label="Pesan balasan"></textarea>
                <button
                    id="admin-send-btn"
                    onclick="AdminChat.sendMessage()"
                    class="w-10 h-10 bg-orange-500 hover:bg-orange-600 text-white rounded-xl flex items-center justify-center flex-shrink-0 transition disabled:opacity-50"
                    aria-label="Kirim">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            <p id="admin-input-error" class="text-xs text-red-500 mt-1 hidden"></p>
        </div>

        {{-- Bot mode notice --}}
        <div id="bot-mode-notice"
            class="{{ $conversation->mode === 'bot' ? '' : 'hidden' }} border-t border-gray-200 p-4 bg-blue-50 flex-shrink-0">
            <p class="text-sm text-blue-700 text-center">
                <i class="fas fa-robot mr-1"></i>
                Percakapan sedang ditangani bot. Klik <strong>Ambil Alih</strong> untuk membalas langsung.
            </p>
        </div>
    </div>

    {{-- Action feedback --}}
    <div id="action-toast" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-sm px-5 py-2 rounded-full shadow-lg z-50"></div>
</div>
@endsection

@push('scripts')
<script>
const AdminChat = (() => {
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const convId = {{ $conversation->id }};
    const BASE = '/admin/chat/conversations/' + convId;

    let state = {
        mode: '{{ $conversation->mode }}',
        lastMessageId: {{ $messages->isNotEmpty() ? $messages->last()['id'] : 0 }},
        polling: null,
        sending: false,
    };

    const el = id => document.getElementById(id);

    function scrollToBottom() {
        const c = el('chat-messages');
        c.scrollTop = c.scrollHeight;
    }

    function showToast(msg, duration = 3000) {
        const t = el('action-toast');
        t.textContent = msg;
        t.classList.remove('hidden');
        setTimeout(() => t.classList.add('hidden'), duration);
    }

    function updateModeUI(mode) {
        state.mode = mode;
        const badge = el('mode-badge');
        const label = el('mode-label');
        const btnTakeover = el('btn-takeover');
        const btnHandback = el('btn-handback');
        const inputArea = el('admin-input-area');
        const botNotice = el('bot-mode-notice');

        if (mode === 'live') {
            badge.className = 'text-sm px-3 py-1 rounded-full font-medium bg-green-100 text-green-700';
            label.textContent = 'Live Mode';
            badge.querySelector('i').className = 'fas fa-user-tie mr-1';
            btnTakeover.classList.add('hidden');
            btnHandback.classList.remove('hidden');
            inputArea.classList.remove('hidden');
            botNotice.classList.add('hidden');
        } else {
            badge.className = 'text-sm px-3 py-1 rounded-full font-medium bg-blue-100 text-blue-700';
            label.textContent = 'Bot Mode';
            badge.querySelector('i').className = 'fas fa-robot mr-1';
            btnTakeover.classList.remove('hidden');
            btnHandback.classList.add('hidden');
            inputArea.classList.add('hidden');
            botNotice.classList.remove('hidden');
        }
    }

    function renderMessage(msg) {
        const isAdmin = msg.sender_type === 'admin';
        const isSystem = msg.sender_type === 'system';
        const isCustomer = msg.sender_type === 'customer';
        const bodyContent = msg.body_html || escapeHtml(msg.body);

        if (isSystem) {
            return `<div class="flex justify-center">
                <span class="text-xs text-gray-500 bg-gray-200 rounded-full px-3 py-1">${escapeHtml(msg.body)}</span>
            </div>`;
        }

        if (isAdmin) {
            return `<div class="flex justify-end">
                <div class="max-w-sm">
                    <div class="bg-orange-500 text-white rounded-2xl rounded-br-none px-3 py-2 shadow-sm text-sm leading-relaxed">${escapeHtml(msg.body)}</div>
                    <p class="text-xs text-gray-400 mt-1 text-right">${msg.created_at} · ${escapeHtml(msg.sender_label)}</p>
                </div>
            </div>`;
        }

        const avatarIcon = isCustomer ? 'fa-user' : 'fa-robot';
        const avatarBg = isCustomer ? 'bg-navy-100' : 'bg-orange-100';
        const avatarColor = isCustomer ? 'text-navy-600' : 'text-orange-500';

        return `<div class="flex items-end space-x-2">
            <div class="w-7 h-7 ${avatarBg} rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas ${avatarIcon} ${avatarColor} text-xs"></i>
            </div>
            <div class="max-w-sm">
                <p class="text-xs text-gray-500 mb-1">${escapeHtml(msg.sender_label)}</p>
                <div class="bg-white border border-gray-200 rounded-2xl rounded-bl-none px-3 py-2 shadow-sm text-sm text-gray-800 leading-relaxed">${bodyContent}</div>
                <p class="text-xs text-gray-400 mt-1">${msg.created_at}</p>
            </div>
        </div>`;
    }

    function appendMessages(messages) {
        const container = el('chat-messages');
        const typing = el('chat-typing');
        let hasNewCustomerMsg = false;

        messages.forEach(msg => {
            if (document.getElementById(`msg-${msg.id}`)) return;
            const div = document.createElement('div');
            div.id = `msg-${msg.id}`;
            div.innerHTML = renderMessage(msg);
            container.insertBefore(div, typing);
            if (msg.id > state.lastMessageId) state.lastMessageId = msg.id;
            if (msg.sender_type === 'customer') hasNewCustomerMsg = true;
        });

        scrollToBottom();

        // Play sound + show toast for new customer messages
        if (hasNewCustomerMsg) {
            const lastCustomer = messages.filter(m => m.sender_type === 'customer').pop();
            AdminChatNotif.notify(
                'Pesan baru dari {{ $conversation->customer?->name ?? "Customer" }}',
                lastCustomer?.body?.substring(0, 80) ?? '',
                null // already on this page
            );
        }
    }

    function escapeHtml(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str ?? ''));
        return d.innerHTML;
    }

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

    // ── Polling ──────────────────────────────────────────────────────────────
    function startPolling() {
        state.polling = setInterval(poll, 3000);
    }

    async function poll() {
        try {
            const data = await apiFetch(`${BASE}/poll?last_id=${state.lastMessageId}`);
            if (data.messages?.length > 0) appendMessages(data.messages);
            if (data.mode !== state.mode) updateModeUI(data.mode);
        } catch (e) {}
    }

    // ── Send ─────────────────────────────────────────────────────────────────
    async function sendMessage() {
        if (state.sending || state.mode !== 'live') return;

        const input = el('admin-chat-input');
        const body = input.value.trim();
        const errorEl = el('admin-input-error');
        errorEl.classList.add('hidden');

        if (!body) return;
        if (body.length > 2000) {
            errorEl.textContent = 'Pesan maksimal 2000 karakter.';
            errorEl.classList.remove('hidden');
            return;
        }

        state.sending = true;
        el('admin-send-btn').disabled = true;
        input.value = '';
        input.style.height = 'auto';

        try {
            const data = await apiFetch(`${BASE}/messages`, {
                method: 'POST',
                body: JSON.stringify({ body }),
            });
            if (data.message) appendMessages([data.message]);
        } catch (e) {
            errorEl.textContent = e.error ?? 'Gagal mengirim pesan.';
            errorEl.classList.remove('hidden');
        } finally {
            state.sending = false;
            el('admin-send-btn').disabled = false;
            input.focus();
        }
    }

    // ── Takeover / Handback ──────────────────────────────────────────────────
    async function takeover() {
        try {
            await apiFetch(`${BASE}/takeover`, { method: 'POST' });
            updateModeUI('live');
            showToast('Berhasil mengambil alih percakapan');
        } catch (e) {
            showToast(e.error ?? 'Gagal mengambil alih.');
        }
    }

    async function handback() {
        try {
            await apiFetch(`${BASE}/handback`, { method: 'POST' });
            updateModeUI('bot');
            showToast('Percakapan dikembalikan ke bot');
        } catch (e) {
            showToast(e.error ?? 'Gagal mengembalikan ke bot.');
        }
    }

    function handleKeydown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    }

    function autoResize(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 128) + 'px';
    }

    // ── Init ─────────────────────────────────────────────────────────────────
    scrollToBottom();
    startPolling();

    return { sendMessage, takeover, handback, handleKeydown, autoResize };
})();
</script>
@endpush
