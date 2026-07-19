{{-- Chat Widget for Customer --}}
<div id="chat-widget" class="fixed bottom-6 right-6 z-50">

    {{-- Toggle Button --}}
    <button id="chat-toggle-btn"
        onclick="ChatWidget.toggle()"
        class="relative w-14 h-14 bg-orange-500 hover:bg-orange-600 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-400"
        aria-label="Buka chat">
        <i class="fas fa-comments text-xl" id="chat-icon-open"></i>
        <i class="fas fa-times text-xl hidden" id="chat-icon-close"></i>
        {{-- Unread badge --}}
        <span id="chat-unread-badge"
            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center hidden">
            0
        </span>
    </button>

    {{-- Chat Window --}}
    <div id="chat-window"
        class="hidden absolute bottom-16 right-0 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden border border-gray-200"
        style="height: 520px;">

        {{-- Header --}}
        <div class="bg-navy-800 px-4 py-3 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-headset text-white text-sm"></i>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm">Multi Base Engineering</p>
                    <p id="chat-status-label" class="text-gray-300 text-xs">Asisten Virtual</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                {{-- Notification bell — hanya muncul jika belum diizinkan --}}
                <button id="chat-notif-btn"
                    onclick="ChatWidget.requestNotificationPermission()"
                    title="Aktifkan notifikasi agar tahu saat ada balasan"
                    class="text-gray-400 hover:text-white focus:outline-none hidden transition"
                    aria-label="Aktifkan notifikasi browser">
                    <i class="fas fa-bell text-sm"></i>
                </button>
                <button onclick="ChatWidget.toggle()" class="text-gray-300 hover:text-white focus:outline-none" aria-label="Tutup chat">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div id="chat-quick-actions" class="px-3 pt-2 pb-1 bg-white border-b border-gray-100 flex-shrink-0 hidden">
            <p class="text-xs text-gray-400 mb-1.5">Pintasan cepat:</p>
            <div class="flex flex-wrap gap-1.5">
                <button onclick="ChatWidget.quickSend('Cek produk yang tersedia')"
                    class="text-xs bg-orange-50 text-orange-600 border border-orange-200 rounded-full px-2.5 py-1 hover:bg-orange-100 transition">
                    🔍 Cek Produk
                </button>
                <button onclick="ChatWidget.quickSend('Saya ingin memesan produk')"
                    class="text-xs bg-blue-50 text-blue-600 border border-blue-200 rounded-full px-2.5 py-1 hover:bg-blue-100 transition">
                    🛒 Buat Pesanan
                </button>
                <button onclick="ChatWidget.quickSend('Bagaimana cara pembayaran?')"
                    class="text-xs bg-green-50 text-green-600 border border-green-200 rounded-full px-2.5 py-1 hover:bg-green-100 transition">
                    💳 Pembayaran
                </button>
                <button onclick="ChatWidget.quickSend('Informasi pengiriman')"
                    class="text-xs bg-purple-50 text-purple-600 border border-purple-200 rounded-full px-2.5 py-1 hover:bg-purple-100 transition">
                    🚚 Pengiriman
                </button>
            </div>
        </div>

        {{-- Messages Area --}}
        <div id="chat-messages"
            class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50"
            onscroll="ChatWidget.handleScroll(this)">

            {{-- Load more --}}
            <div id="chat-load-more" class="text-center hidden">
                <button onclick="ChatWidget.loadMore()"
                    class="text-xs text-navy-600 hover:text-navy-800 underline">
                    Muat pesan sebelumnya
                </button>
            </div>

            {{-- Loading state --}}
            <div id="chat-loading" class="flex justify-center py-4">
                <div class="flex space-x-1">
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0s"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0.15s"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0.3s"></div>
                </div>
            </div>

            {{-- Error state --}}
            <div id="chat-error" class="hidden text-center py-4">
                <p class="text-red-500 text-sm">Gagal memuat percakapan.</p>
                <button onclick="ChatWidget.init()" class="mt-2 text-xs text-navy-600 underline">Coba lagi</button>
            </div>

            {{-- Typing indicator --}}
            <div id="chat-typing" class="hidden">
                <div class="flex items-end space-x-2">
                    <div class="w-7 h-7 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-robot text-orange-500 text-xs"></i>
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

        {{-- Input Area --}}
        <div class="border-t border-gray-200 p-3 bg-white flex-shrink-0">
            <div class="flex items-end space-x-2">
                <textarea
                    id="chat-input"
                    placeholder="Ketik pesan... (contoh: ada baut M10?)"
                    rows="1"
                    maxlength="1000"
                    class="flex-1 resize-none border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent max-h-24 overflow-y-auto"
                    onkeydown="ChatWidget.handleKeydown(event)"
                    oninput="ChatWidget.autoResize(this)"
                    aria-label="Pesan"></textarea>
                <button
                    id="chat-send-btn"
                    onclick="ChatWidget.sendMessage()"
                    class="w-9 h-9 bg-orange-500 hover:bg-orange-600 text-white rounded-xl flex items-center justify-center flex-shrink-0 transition-colors focus:outline-none focus:ring-2 focus:ring-orange-400 disabled:opacity-50"
                    aria-label="Kirim pesan">
                    <i class="fas fa-paper-plane text-sm"></i>
                </button>
            </div>
            <p id="chat-char-count" class="text-xs text-gray-400 mt-1 text-right hidden">0/1000</p>
            <p id="chat-input-error" class="text-xs text-red-500 mt-1 hidden"></p>
        </div>
    </div>
</div>

<script>
const ChatWidget = (() => {
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const BASE = '/customer/chat';

    let state = {
        open: false,
        conversationId: null,
        mode: 'bot',
        lastMessageId: 0,
        page: 1,
        hasMore: false,
        polling: null,
        badgePolling: null,
        sending: false,
        swRegistration: null,
        lastBadgeCount: 0,
    };

    // ── Audio (Web Audio API — no external file needed) ───────────────────────
    let audioCtx = null;

    function playDing() {
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            if (audioCtx.state === 'suspended') audioCtx.resume();
            const now = audioCtx.currentTime;
            _tone(880,  now, 0.0,  0.55, 0.18);
            _tone(1320, now, 0.04, 0.38, 0.10);
        } catch (e) {}
    }

    function _tone(freq, now, delay, dur, gain) {
        const osc  = audioCtx.createOscillator();
        const gn   = audioCtx.createGain();
        osc.connect(gn);
        gn.connect(audioCtx.destination);
        osc.type = 'sine';
        osc.frequency.setValueAtTime(freq, now + delay);
        gn.gain.setValueAtTime(gain, now + delay);
        gn.gain.exponentialRampToValueAtTime(0.001, now + delay + dur);
        osc.start(now + delay);
        osc.stop(now + delay + dur + 0.05);
    }

    // ── In-page toast (muncul di atas tombol chat) ────────────────────────────
    let toastTimer = null;

    function showToast(title, body) {
        let box = document.getElementById('chat-notif-toast');
        if (!box) {
            box = document.createElement('div');
            box.id = 'chat-notif-toast';
            // Posisi di atas tombol chat (bottom-24 right-6)
            box.className = [
                'fixed bottom-24 right-6 z-[9998]',
                'w-72 bg-white border border-gray-200 rounded-2xl shadow-2xl',
                'flex items-start space-x-3 p-3',
                'transform translate-y-2 opacity-0 transition-all duration-300',
                'cursor-pointer select-none',
            ].join(' ');
            document.body.appendChild(box);
        }

        box.innerHTML = `
            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fas fa-comment-dots text-orange-500 text-xs"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-800">${_esc(title)}</p>
                <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">${_esc(body)}</p>
            </div>
            <button onclick="event.stopPropagation(); ChatWidget._dismissToast()"
                class="text-gray-300 hover:text-gray-500 flex-shrink-0 -mt-0.5 p-0.5">
                <i class="fas fa-times text-xs"></i>
            </button>
        `;

        // Klik toast → buka widget
        box.onclick = () => { if (!state.open) toggle(); _dismissToast(); };

        requestAnimationFrame(() => {
            box.classList.remove('translate-y-2', 'opacity-0');
            box.classList.add('translate-y-0', 'opacity-100');
        });

        clearTimeout(toastTimer);
        toastTimer = setTimeout(_dismissToast, 6000);
    }

    function _dismissToast() {
        const box = document.getElementById('chat-notif-toast');
        if (!box) return;
        box.classList.add('translate-y-2', 'opacity-0');
        box.classList.remove('translate-y-0', 'opacity-100');
        clearTimeout(toastTimer);
    }

    function _esc(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str ?? ''));
        return d.innerHTML;
    }

    // ── Browser Notification API ──────────────────────────────────────────────
    function showBrowserNotif(title, body) {
        if (!('Notification' in window) || Notification.permission !== 'granted') return;
        try {
            const n = new Notification(title, {
                body,
                icon: '/favicon.ico',
                tag: 'chat-customer-reply',
                renotify: true,
                silent: true, // suara kita handle sendiri
            });
            n.onclick = () => { window.focus(); if (!state.open) toggle(); n.close(); };
        } catch (e) {}
    }

    // ── Gabungan: suara + toast + browser notif ───────────────────────────────
    function notify(title, body) {
        playDing();
        showToast(title, body);
        showBrowserNotif(title, body);
    }

    // ── Notification permission + VAPID push subscription ────────────────────
    function updateNotifButton() {
        const btn = document.getElementById('chat-notif-btn');
        if (!btn || !('Notification' in window)) return;
        const perm = Notification.permission;
        btn.classList.toggle('hidden', perm === 'granted' || perm === 'denied');
    }

    async function requestNotificationPermission() {
        if (!('Notification' in window)) {
            alert('Browser Anda tidak mendukung notifikasi.');
            return;
        }
        const perm = await Notification.requestPermission();
        updateNotifButton();
        if (perm === 'granted') {
            await _subscribeAndSave();
            notify('Notifikasi Aktif', 'Anda akan mendapat notifikasi + suara saat admin membalas.');
        }
    }

    async function _subscribeAndSave() {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;
        try {
            const reg = await navigator.serviceWorker.ready;
            const res = await apiFetch(`${BASE}/vapid-public-key`);
            if (!res.public_key) return;

            const sub = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: _urlB64ToUint8(res.public_key),
            });
            await apiFetch(`${BASE}/push-subscribe`, {
                method: 'POST',
                body: JSON.stringify({ subscription: sub.toJSON() }),
            });
            state.swRegistration = reg;
        } catch (e) {
            console.warn('Customer push subscription failed:', e);
        }
    }

    function _urlB64ToUint8(b64) {
        const pad = '='.repeat((4 - b64.length % 4) % 4);
        const raw = window.atob((b64 + pad).replace(/-/g, '+').replace(/_/g, '/'));
        return Uint8Array.from(raw, c => c.charCodeAt(0));
    }

    // ── DOM helpers ───────────────────────────────────────────────────────────
    const el = id => document.getElementById(id);

    function showLoading(show) { el('chat-loading').classList.toggle('hidden', !show); }
    function showError(show)   { el('chat-error').classList.toggle('hidden', !show); }

    function showTyping(show) {
        el('chat-typing').classList.toggle('hidden', !show);
        if (show) scrollToBottom();
    }

    function scrollToBottom() {
        const c = el('chat-messages');
        c.scrollTop = c.scrollHeight;
    }

    function updateStatusLabel() {
        el('chat-status-label').textContent =
            state.mode === 'live' ? '🟢 Admin sedang online' : 'Asisten Virtual';
    }

    function updateBadge(count) {
        const badge = el('chat-unread-badge');
        if (count > 0 && !state.open) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    // ── Message rendering ─────────────────────────────────────────────────────
    function renderMessage(msg) {
        const isCustomer = msg.sender_type === 'customer';
        const isSystem   = msg.sender_type === 'system';

        if (isSystem) {
            return `<div class="flex justify-center">
                <span class="text-xs text-gray-500 bg-gray-200 rounded-full px-3 py-1">${_esc(msg.body)}</span>
            </div>`;
        }

        const bodyContent = msg.body_html || _esc(msg.body);
        const avatarIcon  = msg.sender_type === 'admin' ? 'fa-user-tie' : 'fa-robot';
        const avatarBg    = msg.sender_type === 'admin' ? 'bg-navy-700'  : 'bg-orange-100';
        const avatarColor = msg.sender_type === 'admin' ? 'text-white'   : 'text-orange-500';
        const bubbleBg    = isCustomer
            ? 'bg-orange-500 text-white rounded-br-none'
            : 'bg-white border border-gray-200 text-gray-800 rounded-bl-none';

        if (isCustomer) {
            return `<div class="flex justify-end">
                <div class="max-w-xs">
                    <div class="${bubbleBg} rounded-2xl px-3 py-2 shadow-sm text-sm leading-relaxed">${_esc(msg.body)}</div>
                    <p class="text-xs text-gray-400 mt-1 text-right">${msg.created_at}</p>
                </div>
            </div>`;
        }

        return `<div class="flex items-end space-x-2">
            <div class="w-7 h-7 ${avatarBg} rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas ${avatarIcon} ${avatarColor} text-xs"></i>
            </div>
            <div class="max-w-xs">
                <p class="text-xs text-gray-500 mb-1">${_esc(msg.sender_label)}</p>
                <div class="${bubbleBg} rounded-2xl px-3 py-2 shadow-sm text-sm leading-relaxed">${bodyContent}</div>
                <p class="text-xs text-gray-400 mt-1">${msg.created_at}</p>
            </div>
        </div>`;
    }

    function appendMessages(messages, prepend = false) {
        const container = el('chat-messages');
        const typing    = el('chat-typing');
        const loadMore  = el('chat-load-more');
        let hasNewIncoming = false;

        messages.forEach(msg => {
            if (document.getElementById(`msg-${msg.id}`)) return;
            const div = document.createElement('div');
            div.id = `msg-${msg.id}`;
            div.innerHTML = renderMessage(msg);
            if (prepend) {
                container.insertBefore(div, loadMore.nextSibling);
            } else {
                container.insertBefore(div, typing);
            }
            if (msg.id > state.lastMessageId) state.lastMessageId = msg.id;
            if (!prepend && msg.sender_type !== 'customer') hasNewIncoming = true;
        });

        // Notif untuk pesan masuk dari admin/bot saat widget TERBUKA
        if (hasNewIncoming && state.open) {
            const last = messages.filter(m => m.sender_type !== 'customer').pop();
            const senderLabel = last?.sender_type === 'admin' ? 'Admin' : 'Asisten Virtual';
            playDing();
            // Kilat di status label
            const lbl = el('chat-status-label');
            const orig = lbl.textContent;
            lbl.textContent = '💬 Pesan baru!';
            setTimeout(() => { lbl.textContent = orig; }, 2000);
        }
    }

    // ── API calls ─────────────────────────────────────────────────────────────
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
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.json();
    }

    // ── Init ──────────────────────────────────────────────────────────────────
    async function init() {
        showLoading(true);
        showError(false);
        try {
            const data = await apiFetch(`${BASE}/conversation`);
            state.conversationId = data.conversation_id;
            state.mode = data.mode;
            updateStatusLabel();
            await loadMessages();
            el('chat-quick-actions').classList.remove('hidden');
        } catch (e) {
            showLoading(false);
            showError(true);
        }
    }

    async function loadMessages() {
        try {
            const data = await apiFetch(`${BASE}/conversations/${state.conversationId}/messages?page=${state.page}`);
            showLoading(false);
            appendMessages(data.messages);
            state.hasMore = data.has_more;
            state.mode = data.mode;
            updateStatusLabel();
            el('chat-load-more').classList.toggle('hidden', !state.hasMore);
            scrollToBottom();
        } catch (e) {
            showLoading(false);
            showError(true);
        }
    }

    async function loadMore() {
        state.page++;
        const container = el('chat-messages');
        const prevH = container.scrollHeight;
        try {
            const data = await apiFetch(`${BASE}/conversations/${state.conversationId}/messages?page=${state.page}`);
            appendMessages(data.messages, true);
            state.hasMore = data.has_more;
            el('chat-load-more').classList.toggle('hidden', !state.hasMore);
            container.scrollTop = container.scrollHeight - prevH;
        } catch (e) {
            state.page--;
        }
    }

    // ── Polling (widget terbuka) ───────────────────────────────────────────────
    function startPolling() {
        stopPolling();
        state.polling = setInterval(poll, 3000);
    }

    function stopPolling() {
        if (state.polling) { clearInterval(state.polling); state.polling = null; }
    }

    async function poll() {
        if (!state.conversationId) return;
        try {
            const data = await apiFetch(`${BASE}/conversations/${state.conversationId}/poll?last_id=${state.lastMessageId}`);
            if (data.messages?.length > 0) {
                appendMessages(data.messages);
                scrollToBottom();
            }
            if (data.mode !== state.mode) {
                state.mode = data.mode;
                updateStatusLabel();
            }
        } catch (e) {}
    }

    // ── Badge polling (widget tertutup) ───────────────────────────────────────
    async function pollBadge() {
        if (state.open) return;
        try {
            const data = await apiFetch(`${BASE}/unread`);
            const count = data.unread_count ?? 0;

            if (data.conversation_id && !state.conversationId) {
                state.conversationId = data.conversation_id;
            }

            // Ada pesan baru sejak poll terakhir → notif lengkap
            if (count > state.lastBadgeCount && count > 0) {
                notify(
                    'Ada pesan baru',
                    count === 1
                        ? 'Admin atau asisten virtual membalas pesan Anda'
                        : `${count} pesan belum dibaca`
                );
            }

            state.lastBadgeCount = count;
            updateBadge(count);
        } catch (e) {}
    }

    // ── Send message ──────────────────────────────────────────────────────────
    async function sendMessage() {
        if (state.sending || !state.conversationId) return;

        const input   = el('chat-input');
        const body    = input.value.trim();
        const errorEl = el('chat-input-error');
        errorEl.classList.add('hidden');

        if (!body) {
            errorEl.textContent = 'Pesan tidak boleh kosong.';
            errorEl.classList.remove('hidden');
            return;
        }
        if (body.length > 1000) {
            errorEl.textContent = 'Pesan maksimal 1000 karakter.';
            errorEl.classList.remove('hidden');
            return;
        }

        state.sending = true;
        el('chat-send-btn').disabled = true;
        input.value = '';
        input.style.height = 'auto';
        el('chat-char-count').classList.add('hidden');

        if (state.mode === 'bot') showTyping(true);

        try {
            const data = await apiFetch(`${BASE}/conversations/${state.conversationId}/messages`, {
                method: 'POST',
                body: JSON.stringify({ body }),
            });

            showTyping(false);
            if (data.message)     appendMessages([data.message]);
            if (data.bot_message) appendMessages([data.bot_message]);
            scrollToBottom();
        } catch (e) {
            showTyping(false);
            errorEl.textContent = 'Gagal mengirim pesan. Coba lagi.';
            errorEl.classList.remove('hidden');
        } finally {
            state.sending = false;
            el('chat-send-btn').disabled = false;
            input.focus();
        }
    }

    function quickSend(text) {
        el('chat-input').value = text;
        sendMessage();
    }

    // ── UI events ─────────────────────────────────────────────────────────────
    function toggle() {
        state.open = !state.open;
        el('chat-window').classList.toggle('hidden', !state.open);
        el('chat-icon-open').classList.toggle('hidden', state.open);
        el('chat-icon-close').classList.toggle('hidden', !state.open);

        if (state.open) {
            updateBadge(0);
            state.lastBadgeCount = 0;
            updateNotifButton();
            _dismissToast();
            if (!state.conversationId) {
                init();
            } else {
                el('chat-quick-actions').classList.remove('hidden');
            }
            startPolling();
            setTimeout(() => el('chat-input').focus(), 100);
        } else {
            stopPolling();
        }
    }

    function handleKeydown(e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    }

    function autoResize(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 96) + 'px';
        const count = textarea.value.length;
        const countEl = el('chat-char-count');
        countEl.textContent = `${count}/1000`;
        countEl.classList.toggle('hidden', count === 0);
        countEl.classList.toggle('text-red-500', count > 900);
    }

    function handleScroll() {}

    // ── Bootstrap ─────────────────────────────────────────────────────────────
    // Register service worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => { state.swRegistration = reg; })
            .catch(() => {});
    }

    // Init notif button state
    updateNotifButton();

    // Badge poll setiap 8 detik saat widget tertutup
    setInterval(pollBadge, 8000);
    pollBadge();

    return {
        toggle, sendMessage, quickSend,
        handleKeydown, autoResize, handleScroll,
        init, loadMore,
        requestNotificationPermission,
        _dismissToast,
    };
})();
</script>
