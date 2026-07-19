/**
 * AdminChatNotif — shared notification + sound module for admin chat pages.
 * Included on both admin/chat/index and admin/chat/show.
 *
 * Features:
 *  - "DING" sound via Web Audio API (no external file needed)
 *  - In-page toast with customer name, message preview, and link
 *  - Browser Notification API (tab-level, works when tab is open)
 *  - Web Push subscription via VAPID (works when browser is open)
 *  - Mute / unmute toggle persisted in localStorage
 */
const AdminChatNotif = (() => {
    const CSRF = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const STORAGE_MUTE_KEY = 'admin_chat_mute';

    let audioCtx = null;
    let muted = localStorage.getItem(STORAGE_MUTE_KEY) === '1';

    // ── Audio ────────────────────────────────────────────────────────────────

    /**
     * Synthesise a pleasant "DING" chime using Web Audio API.
     * Two-tone: fundamental + overtone, with exponential decay.
     */
    function playDing() {
        if (muted) return;
        try {
            if (!audioCtx) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
            // Resume if suspended (browser autoplay policy)
            if (audioCtx.state === 'suspended') audioCtx.resume();

            const now = audioCtx.currentTime;

            // Fundamental tone — 880 Hz (A5)
            _tone(880, now, 0.0,  0.6, 0.18);
            // Overtone — 1320 Hz (E6), slightly delayed and quieter
            _tone(1320, now, 0.04, 0.4, 0.10);
        } catch (e) {
            // Silently fail if audio is not available
        }
    }

    function _tone(freq, now, startDelay, duration, gain) {
        const osc = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();

        osc.connect(gainNode);
        gainNode.connect(audioCtx.destination);

        osc.type = 'sine';
        osc.frequency.setValueAtTime(freq, now + startDelay);

        gainNode.gain.setValueAtTime(gain, now + startDelay);
        gainNode.gain.exponentialRampToValueAtTime(0.001, now + startDelay + duration);

        osc.start(now + startDelay);
        osc.stop(now + startDelay + duration + 0.05);
    }

    // ── Mute toggle ──────────────────────────────────────────────────────────

    function toggleMute() {
        muted = !muted;
        localStorage.setItem(STORAGE_MUTE_KEY, muted ? '1' : '0');
        _updateMuteButtons();
        return muted;
    }

    function isMuted() { return muted; }

    function _updateMuteButtons() {
        document.querySelectorAll('[data-chat-mute-btn]').forEach(btn => {
            const icon = btn.querySelector('i');
            if (muted) {
                btn.title = 'Suara notifikasi dimatikan — klik untuk aktifkan';
                if (icon) { icon.className = 'fas fa-volume-mute'; }
                btn.classList.add('text-red-400');
                btn.classList.remove('text-gray-500');
            } else {
                btn.title = 'Suara notifikasi aktif — klik untuk matikan';
                if (icon) { icon.className = 'fas fa-volume-up'; }
                btn.classList.remove('text-red-400');
                btn.classList.add('text-gray-500');
            }
        });
    }

    // ── In-page toast ────────────────────────────────────────────────────────

    let toastTimer = null;

    /**
     * Show a rich in-page toast notification.
     * @param {string} title
     * @param {string} body
     * @param {string|null} url  — if provided, clicking the toast navigates there
     */
    function showToast(title, body, url = null) {
        let container = document.getElementById('admin-notif-toast');
        if (!container) {
            container = document.createElement('div');
            container.id = 'admin-notif-toast';
            container.className = [
                'fixed bottom-6 right-6 z-[9999]',
                'w-80 bg-white border border-gray-200 rounded-2xl shadow-2xl',
                'flex items-start space-x-3 p-4',
                'transform translate-y-4 opacity-0 transition-all duration-300',
                'cursor-pointer select-none',
            ].join(' ');
            document.body.appendChild(container);
        }

        container.innerHTML = `
            <div class="w-9 h-9 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fas fa-comment-dots text-orange-500 text-sm"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-800 truncate">${_esc(title)}</p>
                <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">${_esc(body)}</p>
            </div>
            <button onclick="event.stopPropagation(); AdminChatNotif.dismissToast()" class="text-gray-300 hover:text-gray-500 flex-shrink-0 -mt-1 -mr-1 p-1">
                <i class="fas fa-times text-xs"></i>
            </button>
        `;

        if (url) {
            container.onclick = () => { window.location.href = url; };
        } else {
            container.onclick = null;
        }

        // Animate in
        requestAnimationFrame(() => {
            container.classList.remove('translate-y-4', 'opacity-0');
            container.classList.add('translate-y-0', 'opacity-100');
        });

        // Auto-dismiss after 6 seconds
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => dismissToast(), 6000);
    }

    function dismissToast() {
        const container = document.getElementById('admin-notif-toast');
        if (!container) return;
        container.classList.add('translate-y-4', 'opacity-0');
        container.classList.remove('translate-y-0', 'opacity-100');
        clearTimeout(toastTimer);
    }

    function _esc(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str ?? ''));
        return d.innerHTML;
    }

    // ── Browser Notification API ─────────────────────────────────────────────

    function showBrowserNotification(title, body, url = null) {
        if (!('Notification' in window) || Notification.permission !== 'granted') return;
        try {
            const notif = new Notification(title, {
                body,
                icon: '/favicon.ico',
                tag: 'admin-chat-new-msg',
                renotify: true,
                silent: true, // We handle sound ourselves
            });
            if (url) {
                notif.onclick = () => { window.focus(); window.location.href = url; notif.close(); };
            }
        } catch (e) {}
    }

    // ── Full notification (sound + toast + browser notif) ────────────────────

    /**
     * Trigger all notification channels at once.
     */
    function notify(title, body, url = null) {
        playDing();
        showToast(title, body, url);
        showBrowserNotification(title, body, url);
    }

    // ── Push subscription ────────────────────────────────────────────────────

    async function requestPermissionAndSubscribe() {
        if (!('Notification' in window)) {
            alert('Browser Anda tidak mendukung notifikasi.');
            return false;
        }

        const permission = await Notification.requestPermission();
        if (permission !== 'granted') return false;

        _updateNotifButtons();
        await _subscribeAndSave();
        notify('Notifikasi Aktif', 'Anda akan mendapat notifikasi + suara saat ada pesan baru.');
        return true;
    }

    async function _subscribeAndSave() {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;
        try {
            const reg = await navigator.serviceWorker.ready;
            const res = await fetch('/admin/chat/vapid-public-key', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF() },
            });
            const keyData = await res.json();
            if (!keyData.public_key) return;

            const applicationServerKey = _urlBase64ToUint8Array(keyData.public_key);
            const subscription = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey,
            });

            await fetch('/admin/chat/push-subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF(),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ subscription: subscription.toJSON() }),
            });
        } catch (e) {
            console.warn('Admin push subscription failed:', e);
        }
    }

    function _urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) outputArray[i] = rawData.charCodeAt(i);
        return outputArray;
    }

    // ── Button state helpers ─────────────────────────────────────────────────

    function _updateNotifButtons() {
        const granted = ('Notification' in window) && Notification.permission === 'granted';
        document.querySelectorAll('[data-chat-notif-btn]').forEach(btn => {
            btn.classList.toggle('hidden', granted);
        });
    }

    // ── Bootstrap ────────────────────────────────────────────────────────────

    // Register service worker immediately
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    }

    // Init button states on DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        _updateNotifButtons();
        _updateMuteButtons();
    });

    // Also run immediately in case DOM is already ready
    _updateNotifButtons();
    _updateMuteButtons();

    return {
        playDing,
        notify,
        showToast,
        dismissToast,
        showBrowserNotification,
        toggleMute,
        isMuted,
        requestPermissionAndSubscribe,
    };
})();
