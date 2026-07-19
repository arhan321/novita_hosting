@php
    $isAdmin = $msg['sender_type'] === 'admin';
    $isSystem = $msg['sender_type'] === 'system';
    $isCustomer = $msg['sender_type'] === 'customer';
@endphp

<div id="msg-{{ $msg['id'] }}">
    @if($isSystem)
        <div class="flex justify-center">
            <span class="text-xs text-gray-500 bg-gray-200 rounded-full px-3 py-1">
                {{ $msg['body'] }}
            </span>
        </div>
    @elseif($isAdmin)
        <div class="flex justify-end">
            <div class="max-w-sm">
                <div class="bg-orange-500 text-white rounded-2xl rounded-br-none px-3 py-2 shadow-sm text-sm">
                    {{ $msg['body'] }}
                </div>
                <p class="text-xs text-gray-400 mt-1 text-right">
                    {{ $msg['created_at'] }} · {{ $msg['sender_label'] }}
                </p>
            </div>
        </div>
    @else
        @php
            $avatarIcon = $isCustomer ? 'fa-user' : 'fa-robot';
            $avatarBg = $isCustomer ? 'bg-navy-100' : 'bg-orange-100';
            $avatarColor = $isCustomer ? 'text-navy-600' : 'text-orange-500';
        @endphp
        <div class="flex items-end space-x-2">
            <div class="w-7 h-7 {{ $avatarBg }} rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas {{ $avatarIcon }} {{ $avatarColor }} text-xs"></i>
            </div>
            <div class="max-w-sm">
                <p class="text-xs text-gray-500 mb-1">{{ $msg['sender_label'] }}</p>
                <div class="bg-white border border-gray-200 rounded-2xl rounded-bl-none px-3 py-2 shadow-sm text-sm text-gray-800">
                    {{ $msg['body'] }}
                </div>
                <p class="text-xs text-gray-400 mt-1">{{ $msg['created_at'] }}</p>
            </div>
        </div>
    @endif
</div>
