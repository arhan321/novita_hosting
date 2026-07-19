<?php

namespace App\Services;

use App\Models\ChatSetting;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Illuminate\Support\Facades\Log;

class WebPushService
{
    private ?WebPush $webPush = null;

    private function getWebPush(): WebPush
    {
        if ($this->webPush === null) {
            $auth = [
                'VAPID' => [
                    'subject'    => config('webpush.vapid.subject'),
                    'publicKey'  => config('webpush.vapid.public_key'),
                    'privateKey' => config('webpush.vapid.private_key'),
                ],
            ];
            $this->webPush = new WebPush($auth);
            $this->webPush->setDefaultOptions([
                'TTL' => 86400, // 24 hours
                'urgency' => 'normal',
            ]);
        }
        return $this->webPush;
    }

    /**
     * Send a push notification to a single subscription.
     */
    public function sendToSubscription(array $subscriptionData, array $payload): bool
    {
        if (empty($subscriptionData['endpoint'])) {
            return false;
        }

        try {
            $subscription = Subscription::create($subscriptionData);
            $webPush = $this->getWebPush();

            $report = $webPush->sendOneNotification(
                $subscription,
                json_encode($payload)
            );

            if (!$report->isSuccess()) {
                Log::warning('WebPush failed', [
                    'endpoint' => substr($subscriptionData['endpoint'], 0, 60),
                    'reason'   => $report->getReason(),
                ]);

                // If subscription expired/invalid, remove it
                if ($report->isSubscriptionExpired()) {
                    return false; // Caller should remove this subscription
                }
            }

            return $report->isSuccess();
        } catch (\Throwable $e) {
            Log::warning('WebPush exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send push notification to a customer by their user ID.
     */
    public function notifyCustomer(int $customerId, string $title, string $body, string $url = '/'): void
    {
        $key = 'push_subscription_customer_' . $customerId;
        $subscriptionJson = ChatSetting::get($key);

        if (!$subscriptionJson) {
            return;
        }

        $subscriptionData = json_decode($subscriptionJson, true);
        if (empty($subscriptionData)) {
            return;
        }

        $payload = [
            'title' => $title,
            'body'  => $body,
            'tag'   => 'chat-customer-' . $customerId,
            'url'   => $url,
            'icon'  => '/favicon.ico',
        ];

        $success = $this->sendToSubscription($subscriptionData, $payload);

        // Clean up expired subscription
        if (!$success) {
            ChatSetting::where('key', $key)->delete();
        }
    }

    /**
     * Send push notification to all subscribed admins.
     */
    public function notifyAllAdmins(string $title, string $body, string $url = '/admin/chat'): void
    {
        $settings = ChatSetting::where('key', 'like', 'push_subscription_admin_%')->get();

        foreach ($settings as $setting) {
            $subscriptionData = json_decode($setting->value, true);
            if (empty($subscriptionData)) {
                continue;
            }

            $payload = [
                'title' => $title,
                'body'  => $body,
                'tag'   => 'chat-admin-new-message',
                'url'   => $url,
                'icon'  => '/favicon.ico',
            ];

            $success = $this->sendToSubscription($subscriptionData, $payload);

            // Clean up expired subscription
            if (!$success) {
                $setting->delete();
            }
        }
    }

    /**
     * Get the VAPID public key for the frontend subscription.
     */
    public function getVapidPublicKey(): string
    {
        return config('webpush.vapid.public_key', '');
    }
}
