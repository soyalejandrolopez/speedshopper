<?php

namespace App\Services;

use App\Models\Setting;
use App\Notifications\StatusChangedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class StatusNotifier
{
    /**
     * Notify the customer whenever an order, package or shipment changes status.
     */
    public function notify(mixed $statusable, string $from, string $to): void
    {
        if ($from === $to) {
            return;
        }

        $customer = $statusable->customer ?? null;

        if (! $customer) {
            return;
        }

        try {
            if (Setting::get('notify_email', '1') === '1') {
                $email = $customer->user?->email ?? $customer->email;

                if ($email) {
                    Notification::route('mail', $email)
                        ->notify($this->notification($statusable, $from, $to));
                }
            }

            if (Setting::get('notify_whatsapp', '0') === '1' && $customer->whatsapp) {
                Notification::route('whatsapp', $customer->whatsapp)
                    ->notify($this->notification($statusable, $from, $to));
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to send status notification: '.$e->getMessage());
        }
    }

    protected function notification(mixed $statusable, string $from, string $to): \Illuminate\Notifications\Notification
    {
        return new StatusChangedNotification($statusable, $from, $to);
    }
}
