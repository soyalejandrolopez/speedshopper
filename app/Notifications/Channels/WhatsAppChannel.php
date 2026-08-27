<?php

namespace App\Notifications\Channels;

use App\Models\Setting;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        $url = Setting::get('whatsapp_api_url');

        if (! $url || ! method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $phone = $notifiable->routeNotificationFor('whatsapp');

        if (! $phone || ! ($message = $notification->toWhatsApp($notifiable))) {
            return;
        }

        $payload = [
            'phone' => $phone,
            'message' => $message,
        ];

        try {
            $response = Http::timeout(10)
                ->withToken((string) Setting::get('whatsapp_api_token', ''))
                ->asJson()
                ->post($url, $payload);

            if ($response->failed()) {
                Log::warning('WhatsApp notification failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'phone' => $payload['phone'],
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('WhatsApp notification error: '.$e->getMessage(), [
                'phone' => $payload['phone'],
            ]);
        }
    }
}
