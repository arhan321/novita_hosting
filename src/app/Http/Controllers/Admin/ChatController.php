<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\KnowledgeBase;
use App\Models\ChatSetting;
use App\Models\Message;
use App\Services\WebPushService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function __construct(private WebPushService $webPushService) {}
    /**
     * Show the admin chat management page.
     */
    public function index(Request $request)
    {
        $mode = $request->get('mode', 'all');

        $query = Conversation::with(['customer', 'messages' => function ($q) {
            $q->latest()->limit(1);
        }])->active();

        if (in_array($mode, ['bot', 'live'])) {
            $query->where('mode', $mode);
        }

        $conversations = $query->orderByDesc('updated_at')->paginate(20);

        // Total unread conversations count for badge
        $unreadConversationsCount = Conversation::active()
            ->whereHas('messages', fn($q) => $q->where('is_read_by_admin', false)->where('sender_type', 'customer'))
            ->count();

        return view('admin.chat.index', compact('conversations', 'mode', 'unreadConversationsCount'));
    }

    /**
     * Show a specific conversation detail.
     */
    public function show(Conversation $conversation)
    {
        $conversation->load('customer');
        $conversation->markAdminRead();

        $messages = $conversation->messages()
            ->orderBy('created_at')
            ->get()
            ->map(fn($m) => $this->formatMessage($m));

        return view('admin.chat.show', compact('conversation', 'messages'));
    }

    /**
     * Poll for new messages in a conversation.
     */
    public function pollMessages(Request $request, Conversation $conversation): JsonResponse
    {
        $lastId = (int) $request->get('last_id', 0);

        $messages = $conversation->messages()
            ->where('id', '>', $lastId)
            ->orderBy('created_at')
            ->get();

        if ($messages->isNotEmpty()) {
            $conversation->markAdminRead();
        }

        return response()->json([
            'messages' => $messages->map(fn($m) => $this->formatMessage($m)),
            'mode' => $conversation->fresh()->mode,
        ]);
    }

    /**
     * Poll for new conversations or updates (for the list page).
     */
    public function pollConversations(Request $request): JsonResponse
    {
        $mode = $request->get('mode', 'all');

        $query = Conversation::with(['customer'])->active();

        if (in_array($mode, ['bot', 'live'])) {
            $query->where('mode', $mode);
        }

        $conversations = $query->orderByDesc('updated_at')->take(50)->get();

        $unreadConversationsCount = Conversation::active()
            ->whereHas('messages', fn($q) => $q->where('is_read_by_admin', false)->where('sender_type', 'customer'))
            ->count();

        return response()->json([
            'conversations' => $conversations->map(fn($c) => $this->formatConversation($c)),
            'unread_count' => $unreadConversationsCount,
        ]);
    }

    /**
     * Send a message as admin.
     */
    public function sendMessage(Request $request, Conversation $conversation): JsonResponse
    {
        if (!$conversation->isLiveMode()) {
            return response()->json(['error' => 'Conversation tidak dalam Live Mode.'], 422);
        }

        $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:2000'],
        ]);

        $body = $request->input('body');

        if ($this->containsXss($body)) {
            return response()->json(['error' => 'Konten pesan tidak diizinkan.'], 422);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'sender_type' => 'admin',
            'body' => htmlspecialchars($body, ENT_QUOTES, 'UTF-8'),
            'is_read_by_admin' => true,
            'is_read_by_customer' => false,
        ]);

        // Touch conversation to update updated_at
        $conversation->touch();

        // Send push notification to customer via Web Push
        $this->webPushService->notifyCustomer(
            $conversation->customer_id,
            'Multi Base Engineering',
            'Admin membalas: ' . mb_substr(strip_tags($body), 0, 80),
            '/customer/dashboard'
        );

        return response()->json(['message' => $this->formatMessage($message)]);
    }

    /**
     * Store push notification subscription for admin.
     */
    public function pushSubscribe(Request $request): JsonResponse
    {
        $request->validate([
            'subscription'          => ['required', 'array'],
            'subscription.endpoint' => ['required', 'string', 'url'],
        ]);

        $admin = Auth::user();
        $key = 'push_subscription_admin_' . $admin->id;
        ChatSetting::set($key, json_encode($request->input('subscription')));

        return response()->json(['success' => true]);
    }

    /**
     * Return the VAPID public key for frontend subscription.
     */
    public function vapidPublicKey(): JsonResponse
    {
        return response()->json([
            'public_key' => $this->webPushService->getVapidPublicKey(),
        ]);
    }

    /**
     * Takeover a conversation (bot → live).
     */
    public function takeover(Conversation $conversation): JsonResponse
    {
        if (!$conversation->isBotMode()) {
            if ($conversation->isLiveMode()) {
                $handlerName = $conversation->handler?->name ?? 'Admin lain';
                return response()->json([
                    'error' => "Conversation sudah ditangani oleh {$handlerName}.",
                ], 422);
            }
            return response()->json(['error' => 'Conversation tidak dalam Bot Mode.'], 422);
        }

        $admin = Auth::user();

        $conversation->update([
            'mode' => 'live',
            'handled_by' => $admin->id,
            'taken_over_at' => now(),
            'taken_over_by_name' => $admin->name,
        ]);

        // System message to customer
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => null,
            'sender_type' => 'system',
            'body' => "Admin {$admin->name} telah bergabung dalam percakapan dan siap membantu Anda.",
            'is_read_by_admin' => true,
            'is_read_by_customer' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil alih percakapan.',
            'mode' => 'live',
        ]);
    }

    /**
     * Handback a conversation (live → bot).
     */
    public function handback(Conversation $conversation): JsonResponse
    {
        if (!$conversation->isLiveMode()) {
            return response()->json(['error' => 'Conversation tidak dalam Live Mode.'], 422);
        }

        $admin = Auth::user();

        $conversation->update([
            'mode' => 'bot',
            'handled_by' => null,
            'handed_back_at' => now(),
            'handed_back_by_name' => $admin->name,
        ]);

        // System message to customer
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => null,
            'sender_type' => 'system',
            'body' => 'Percakapan telah dikembalikan ke asisten virtual. Bot kami siap membantu Anda kembali.',
            'is_read_by_admin' => true,
            'is_read_by_customer' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengembalikan percakapan ke bot.',
            'mode' => 'bot',
        ]);
    }

    // ─── Knowledge Base ───────────────────────────────────────────────────────

    public function knowledgeBase()
    {
        $entries = KnowledgeBase::orderBy('category')->orderBy('id')->paginate(20);
        $threshold = ChatSetting::get('confidence_threshold', '0.4');
        return view('admin.chat.knowledge-base', compact('entries', 'threshold'));
    }

    public function storeKnowledge(Request $request): JsonResponse
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'min:1', 'max:500'],
            'answer'   => ['required', 'string', 'min:1', 'max:2000'],
            'category' => ['required', 'string', 'min:1', 'max:100'],
        ]);

        $entry = KnowledgeBase::create($data + ['is_active' => true]);

        return response()->json(['success' => true, 'entry' => $entry]);
    }

    public function updateKnowledge(Request $request, KnowledgeBase $knowledge): JsonResponse
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'min:1', 'max:500'],
            'answer'   => ['required', 'string', 'min:1', 'max:2000'],
            'category' => ['required', 'string', 'min:1', 'max:100'],
        ]);

        $knowledge->update($data);

        return response()->json(['success' => true, 'entry' => $knowledge->fresh()]);
    }

    public function destroyKnowledge(KnowledgeBase $knowledge): JsonResponse
    {
        $knowledge->delete();
        return response()->json(['success' => true]);
    }

    public function toggleKnowledge(KnowledgeBase $knowledge): JsonResponse
    {
        $knowledge->update(['is_active' => !$knowledge->is_active]);
        return response()->json(['success' => true, 'is_active' => $knowledge->is_active]);
    }

    public function updateThreshold(Request $request): JsonResponse
    {
        $request->validate([
            'threshold' => ['required', 'numeric', 'min:0.1', 'max:1.0'],
        ]);

        ChatSetting::set('confidence_threshold', (string) $request->input('threshold'));

        return response()->json(['success' => true]);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function formatMessage(Message $message): array
    {
        return [
            'id' => $message->id,
            'body' => $message->body,
            'body_html' => $this->formatBodyHtml($message->body),
            'sender_type' => $message->sender_type,
            'sender_label' => $message->getSenderLabel(),
            'created_at' => $message->created_at->format('H:i'),
            'created_at_full' => $message->created_at->format('d/m/Y H:i'),
        ];
    }

    private function formatConversation(Conversation $conversation): array
    {
        $lastMessage = $conversation->messages()->latest()->first();
        return [
            'id' => $conversation->id,
            'customer_name' => $conversation->customer?->name ?? 'Unknown',
            'mode' => $conversation->mode,
            'unread_count' => $conversation->unreadCountForAdmin(),
            'last_message' => $lastMessage ? mb_substr($lastMessage->body, 0, 100) : '',
            'last_message_at' => $lastMessage ? $lastMessage->created_at->format('d/m/Y H:i') : '',
            'updated_at' => $conversation->updated_at->format('d/m/Y H:i'),
        ];
    }

    private function containsXss(string $input): bool
    {
        $patterns = [
            '/<script\b[^>]*>/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert plain text with markdown-like formatting to safe HTML.
     */
    private function formatBodyHtml(string $body): string
    {
        $html = htmlspecialchars($body, ENT_QUOTES, 'UTF-8');
        $html = preg_replace('/\*([^*\n]+)\*/', '<strong>$1</strong>', $html);
        $html = preg_replace(
            '/(https?:\/\/[^\s<>"]+)/i',
            '<a href="$1" target="_blank" rel="noopener noreferrer" class="underline text-blue-600 hover:text-blue-800 break-all">$1</a>',
            $html
        );
        $html = nl2br($html);
        return $html;
    }

    /**
     * Send a Web Push notification to the customer of a conversation.
     * Uses the Voluntary Application Server Identification (VAPID) approach
     * via a simple HTTP call — no external library required for basic usage.
     * Falls back silently if no subscription is stored.
     */
    private function sendPushToCustomer(Conversation $conversation, string $messagePreview): void
    {
        $customerId = $conversation->customer_id;
        $key = 'push_subscription_customer_' . $customerId;
        $subscriptionJson = ChatSetting::get($key);

        if (!$subscriptionJson) {
            return;
        }

        $subscription = json_decode($subscriptionJson, true);
        if (empty($subscription['endpoint'])) {
            return;
        }

        $payload = json_encode([
            'title' => 'Multi Base Engineering',
            'body' => 'Admin membalas: ' . mb_substr(strip_tags($messagePreview), 0, 80),
            'tag' => 'chat-admin-reply',
            'url' => '/customer/dashboard',
        ]);

        $this->dispatchWebPush($subscription, $payload);
    }

    /**
     * Send a Web Push notification to all subscribed admins about a new customer message.
     */
    public function notifyAdminsNewMessage(Conversation $conversation, string $customerName, string $messagePreview): void
    {
        // Find all admin push subscriptions
        $settings = \App\Models\ChatSetting::where('key', 'like', 'push_subscription_admin_%')->get();

        $payload = json_encode([
            'title' => 'Pesan Baru dari ' . $customerName,
            'body' => mb_substr(strip_tags($messagePreview), 0, 80),
            'tag' => 'chat-new-message-' . $conversation->id,
            'url' => '/admin/chat/conversations/' . $conversation->id,
        ]);

        foreach ($settings as $setting) {
            $subscription = json_decode($setting->value, true);
            if (!empty($subscription['endpoint'])) {
                $this->dispatchWebPush($subscription, $payload);
            }
        }
    }

    /**
     * Dispatch a Web Push notification.
     * This is a lightweight implementation without VAPID signing.
     * For production, use a library like minishlink/web-push.
     */
    private function dispatchWebPush(array $subscription, string $payload): void
    {
        try {
            $endpoint = $subscription['endpoint'];

            // Build headers
            $headers = [
                'Content-Type: application/octet-stream',
                'TTL: 86400',
            ];

            // If auth keys are present, add them (basic support)
            if (!empty($subscription['keys']['auth']) && !empty($subscription['keys']['p256dh'])) {
                // For full VAPID encryption, use minishlink/web-push package.
                // Here we send an unencrypted push (works for basic notifications
                // on some browsers, but encrypted is recommended for production).
                $headers[] = 'Encryption: salt=' . base64_encode(random_bytes(16));
            }

            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);
            curl_exec($ch);
            curl_close($ch);
        } catch (\Throwable $e) {
            Log::warning('Push notification failed: ' . $e->getMessage());
        }
    }
}
