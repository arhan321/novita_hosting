<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ChatSetting;
use App\Services\BotService;
use App\Services\WebPushService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct(
        private BotService $botService,
        private WebPushService $webPushService,
    ) {}

    /**
     * Get or create the active conversation for the authenticated customer.
     */
    public function getOrCreateConversation(): JsonResponse
    {
        $customer = Auth::user();

        $conversation = Conversation::active()
            ->forCustomer($customer->id)
            ->latest()
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'customer_id' => $customer->id,
                'mode' => 'bot',
                'is_active' => true,
                'expires_at' => now()->addDays(90),
            ]);

            // Send welcome message from bot
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => null,
                'sender_type' => 'bot',
                'body' => "Halo! Selamat datang di layanan chat Multi Base Engineering. 👋\n\nSaya adalah asisten virtual yang siap membantu Anda.\n\nAnda bisa:\n• Tanya ketersediaan produk — ketik \"ada [nama produk]?\"\n• Buat pesanan (PO) — ketik \"pesan [nama produk]\"\n• Tanya informasi umum seputar layanan kami\n\nSilakan ajukan pertanyaan Anda!",
                'is_read_by_admin' => true,
                'is_read_by_customer' => false,
            ]);
        }

        return response()->json([
            'conversation_id' => $conversation->id,
            'mode' => $conversation->mode,
        ]);
    }

    /**
     * Load messages for the customer's conversation (paginated).
     */
    public function getMessages(Request $request, Conversation $conversation): JsonResponse
    {
        // Authorization: customer can only access their own conversation
        if ($conversation->customer_id !== Auth::id()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $page = (int) $request->get('page', 1);
        $perPage = 50;

        $total = $conversation->messages()->count();
        $messages = $conversation->messages()
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->reverse()
            ->values();

        // Mark messages as read by customer
        $conversation->markCustomerRead();

        return response()->json([
            'messages' => $messages->map(fn($m) => $this->formatMessage($m)),
            'mode' => $conversation->mode,
            'total' => $total,
            'has_more' => $total > $page * $perPage,
        ]);
    }

    /**
     * Poll for new messages since a given message ID.
     */
    public function pollMessages(Request $request, Conversation $conversation): JsonResponse
    {
        if ($conversation->customer_id !== Auth::id()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $lastId = (int) $request->get('last_id', 0);

        $messages = $conversation->messages()
            ->where('id', '>', $lastId)
            ->orderBy('created_at')
            ->get();

        if ($messages->isNotEmpty()) {
            $conversation->markCustomerRead();
        }

        return response()->json([
            'messages' => $messages->map(fn($m) => $this->formatMessage($m)),
            'mode' => $conversation->fresh()->mode,
            'unread_count' => $conversation->fresh()->unreadCountForCustomer(),
        ]);
    }

    /**
     * Send a message from the customer.
     */
    public function sendMessage(Request $request, Conversation $conversation): JsonResponse
    {
        if ($conversation->customer_id !== Auth::id()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:1000'],
        ]);

        $body = $request->input('body');

        // XSS check
        if ($this->containsXss($body)) {
            return response()->json(['error' => 'Konten pesan tidak diizinkan.'], 422);
        }

        // Save customer message
        $customerMessage = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'sender_type' => 'customer',
            'body' => htmlspecialchars($body, ENT_QUOTES, 'UTF-8'),
            'is_read_by_admin' => false,
            'is_read_by_customer' => true,
        ]);

        $response = ['message' => $this->formatMessage($customerMessage)];

        // If in bot mode, process bot response
        if ($conversation->isBotMode()) {
            $botAnswer = $this->botService->respond($conversation, $body);

            if ($botAnswer !== null) {
                $botMessage = Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => null,
                    'sender_type' => 'bot',
                    'body' => $botAnswer,
                    'is_read_by_admin' => true,
                    'is_read_by_customer' => false,
                ]);
                $response['bot_message'] = $this->formatMessage($botMessage);
            } else {
                // Fallback: notify admin
                $fallback = $this->botService->getFallbackMessage();
                $fallbackMessage = Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => null,
                    'sender_type' => 'bot',
                    'body' => $fallback,
                    'is_read_by_admin' => true,
                    'is_read_by_customer' => false,
                ]);
                $response['bot_message'] = $this->formatMessage($fallbackMessage);
                $response['needs_admin'] = true;
            }
        }

        // Touch conversation to update updated_at (triggers admin poll)
        $conversation->touch();

        // Notify admins about new customer message via Web Push
        $this->webPushService->notifyAllAdmins(
            'Pesan Baru dari ' . Auth::user()->name,
            mb_substr(strip_tags($body), 0, 80),
            '/admin/chat/conversations/' . $conversation->id
        );

        return response()->json($response);
    }

    /**
     * Get unread count for the customer's widget badge.
     */
    public function getUnreadCount(): JsonResponse
    {
        $customer = Auth::user();

        $conversation = Conversation::active()
            ->forCustomer($customer->id)
            ->latest()
            ->first();

        if (!$conversation) {
            return response()->json(['unread_count' => 0]);
        }

        return response()->json([
            'unread_count' => $conversation->unreadCountForCustomer(),
            'conversation_id' => $conversation->id,
        ]);
    }

    /**
     * Store push notification subscription for the customer.
     */
    public function pushSubscribe(Request $request): JsonResponse
    {
        $request->validate([
            'subscription'           => ['required', 'array'],
            'subscription.endpoint'  => ['required', 'string', 'url'],
        ]);

        $customer = Auth::user();
        $key = 'push_subscription_customer_' . $customer->id;
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

    /**
     * Convert plain text with markdown-like formatting to safe HTML.
     * Supports: *bold*, newlines, and URLs.
     */
    private function formatBodyHtml(string $body): string
    {
        // Escape HTML first
        $html = htmlspecialchars($body, ENT_QUOTES, 'UTF-8');

        // Convert *text* to <strong>text</strong>
        $html = preg_replace('/\*([^*\n]+)\*/', '<strong>$1</strong>', $html);

        // Convert URLs to clickable links
        $html = preg_replace(
            '/(https?:\/\/[^\s<>"]+)/i',
            '<a href="$1" target="_blank" rel="noopener noreferrer" class="underline text-blue-600 hover:text-blue-800 break-all">$1</a>',
            $html
        );

        // Convert newlines to <br>
        $html = nl2br($html);

        return $html;
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
}
