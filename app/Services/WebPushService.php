<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Participant;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    /**
     * Send a Web Push notification to a specific participant across all their active devices.
     */
    public function sendToParticipant(Participant $participant, string $title, string $body, string $url): int
    {
        $subscriptions = $participant->pushSubscriptions()->get();

        if ($subscriptions->isEmpty()) {
            return 0;
        }

        $vapidSubject = config('services.vapid.subject');
        $vapidPublic = config('services.vapid.public_key');
        $vapidPrivate = config('services.vapid.private_key');

        if (empty($vapidPublic) || empty($vapidPrivate)) {
            Log::warning('WebPushService: chaves VAPID não configuradas.');

            return 0;
        }

        $auth = [
            'VAPID' => [
                'subject' => $vapidSubject,
                'publicKey' => $vapidPublic,
                'privateKey' => $vapidPrivate,
            ],
        ];

        try {
            $webPush = new WebPush($auth);
            $webPush->setReuseVAPIDHeaders(true);

            $payload = json_encode([
                'title' => $title,
                'body' => $body,
                'url' => $url,
                'icon' => '/apple-touch-icon.png',
                'badge' => '/favicon-32x32.png',
            ]);

            $queuedCount = 0;
            $subscriptionMap = [];

            foreach ($subscriptions as $sub) {
                try {
                    $subscription = Subscription::create([
                        'endpoint' => $sub->endpoint,
                        'publicKey' => $sub->public_key,
                        'authToken' => $sub->auth_token,
                        'contentEncoding' => $sub->content_encoding ?: 'aes128gcm',
                    ]);

                    $webPush->queueNotification($subscription, $payload);
                    $subscriptionMap[$sub->endpoint] = $sub;
                    $queuedCount++;
                } catch (\Throwable $e) {
                    Log::warning("WebPushService: Erro ao enfileirar subscrição {$sub->id}: {$e->getMessage()}");
                }
            }

            if ($queuedCount === 0) {
                return 0;
            }

            $successCount = 0;

            foreach ($webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                $subModel = $subscriptionMap[$endpoint] ?? null;

                if ($report->isSuccess()) {
                    $successCount++;
                } else {
                    $reason = $report->getReason();
                    Log::info("WebPushService: Falha de envio para {$endpoint}: {$reason}");

                    // If device unregistered, expired, or rejected, delete stale subscription
                    if ($report->isSubscriptionExpired() && $subModel) {
                        Log::info("WebPushService: Removendo subscrição expirada {$subModel->id}");
                        $subModel->delete();
                    }
                }
            }

            return $successCount;
        } catch (\Throwable $e) {
            Log::error("WebPushService: Erro geral no envio de Web Push: {$e->getMessage()}");

            return 0;
        }
    }

    /**
     * Send draw announcement push notifications to all participants of an event.
     */
    public function sendDrawNotifications(Event $event): int
    {
        $participants = $event->participants()
            ->whereHas('pushSubscriptions')
            ->get();

        $totalSent = 0;

        foreach ($participants as $participant) {
            $title = 'GiftMatch 🎁 Sorteio Realizado!';
            $body = "O Amigo Secreto do evento \"{$event->title}\" foi sorteado! Toque para ver quem você tirou.";
            $url = route('secret.show', ['token' => $participant->token]);

            $sent = $this->sendToParticipant($participant, $title, $body, $url);
            $totalSent += $sent;
        }

        return $totalSent;
    }
}
