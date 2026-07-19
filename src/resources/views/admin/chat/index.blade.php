@extends('layouts.app')

@section('title', 'Manajemen Chat')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-navy-800">
                <i class="fas fa-comments text-orange-500 mr-2"></i>
                Manajemen Chat
                @if($unreadConversationsCount > 0)
                    <span class="ml-2 bg-red-500 text-white text-sm font-bold rounded-full px-2 py-0.5">
                        {{ $unreadConversationsCount > 99 ? '99+' : $unreadConversationsCount }}
                    </span>
                @endif
            </h1>
            <p class="text-gray-500 text-sm mt-1">Monitor dan tangani percakapan customer</p>
        </div>
        <div class="flex items-center space-x-2">
            {{-- Notification bell --}}
            <button data-chat-notif-btn
                onclick="AdminChatNotif.requestPermissionAndSubscribe()"
                title="Aktifkan notifikasi browser untuk pesan baru"
                class="hidden border border-gray-200 text-gray-500 hover:text-gray-700 text-sm px-3 py-2 rounded-lg transition">
                <i class="fas fa-bell mr-1"></i> Aktifkan Notifikasi
            </button>
            {{-- Mute / unmute sound --}}
            <button data-chat-mute-btn
                onclick="AdminChatNotif.toggleMute()"
                title="Suara notifikasi aktif — klik untuk matikan"
                class="border border-gray-200 text-gray-500 hover:text-gray-700 px-3 py-2 rounded-lg transition">
                <i class="fas fa-volume-up"></i>
            </button>
            <a href="{{ route('admin.chat.knowledge-base') }}"
                class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                <i class="fas fa-book mr-1"></i> Knowledge Base
            </a>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex space-x-2 mb-4">
        @foreach(['all' => 'Semua', 'bot' => 'Bot Mode', 'live' => 'Live Mode'] as $key => $label)
            <a href="{{ route('admin.chat.index', ['mode' => $key]) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition
                    {{ $mode === $key
                        ? 'bg-orange-500 text-white shadow'
                        : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Conversations List --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        @if($conversations->isEmpty())
            <div class="text-center py-16 text-gray-400">
                <i class="fas fa-comment-slash text-4xl mb-3"></i>
                <p>Belum ada percakapan</p>
            </div>
        @else
            <div id="conversations-list">
                @foreach($conversations as $conv)
                    @php
                        $lastMsg = $conv->messages->first();
                        $unread = $conv->unreadCountForAdmin();
                    @endphp
                    <a href="{{ route('admin.chat.show', $conv) }}"
                        class="flex items-center px-5 py-4 hover:bg-gray-50 border-b border-gray-100 transition group">
                        {{-- Avatar --}}
                        <div class="w-10 h-10 bg-navy-100 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                            <i class="fas fa-user text-navy-600"></i>
                        </div>
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-gray-800 text-sm truncate">
                                    {{ $conv->customer?->name ?? 'Unknown' }}
                                </p>
                                <span class="text-xs text-gray-400 ml-2 flex-shrink-0">
                                    {{ $lastMsg ? $lastMsg->created_at->format('d/m/Y H:i') : '' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between mt-0.5">
                                <p class="text-xs text-gray-500 truncate">
                                    {{ $lastMsg ? Str::limit($lastMsg->body, 60) : 'Belum ada pesan' }}
                                </p>
                                <div class="flex items-center space-x-2 ml-2 flex-shrink-0">
                                    {{-- Mode badge --}}
                                    <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                        {{ $conv->mode === 'live' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $conv->mode === 'live' ? 'Live' : 'Bot' }}
                                    </span>
                                    {{-- Unread badge --}}
                                    @if($unread > 0)
                                        <span class="bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                                            {{ $unread > 99 ? '99+' : $unread }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($conversations->hasPages())
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $conversations->appends(['mode' => $mode])->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
const AdminChatList = (() => {
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    let lastUnreadCount = {{ $unreadConversationsCount }};

    // ── Polling ──────────────────────────────────────────────────────────────
    async function pollConversations() {
        try {
            const mode = new URLSearchParams(window.location.search).get('mode') || 'all';
            const res = await fetch(`/admin/chat/conversations/poll?mode=${mode}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            });
            if (!res.ok) return;
            const data = await res.json();

            const newCount = data.unread_count ?? 0;

            if (newCount > lastUnreadCount) {
                // Find the conversation(s) that are new/updated
                const newConv = (data.conversations ?? []).find(c => c.unread_count > 0);
                const title = newConv
                    ? `Pesan baru dari ${newConv.customer_name}`
                    : 'Ada pesan baru';
                const body = newConv?.last_message
                    ? newConv.last_message.substring(0, 80)
                    : `${newCount} percakapan belum dibaca`;
                const url = newConv ? `/admin/chat/conversations/${newConv.id}` : '/admin/chat';

                AdminChatNotif.notify(title, body, url);
            }

            lastUnreadCount = newCount;
        } catch (e) {}
    }

    setInterval(pollConversations, 5000);

    return {};
})();
</script>
@endpush