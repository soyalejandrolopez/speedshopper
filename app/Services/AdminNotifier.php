<?php

namespace App\Services;

use App\Models\ContactInquiry;
use App\Models\PurchaseRequest;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\NewContactInquiryNotification;
use App\Notifications\NewPurchaseRequestNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AdminNotifier
{
    /**
     * Notify administrators about a newly created purchase request.
     */
    public static function notifyNewPurchaseRequest(PurchaseRequest $request): void
    {
        if (Setting::get('notify_email', '1') !== '1') {
            return;
        }

        try {
            $emails = self::getAdminEmails();

            foreach ($emails as $email) {
                Notification::route('mail', $email)
                    ->notify(new NewPurchaseRequestNotification($request));
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to notify admin about new purchase request: '.$e->getMessage());
        }
    }

    /**
     * Notify administrators about a newly submitted contact inquiry.
     */
    public static function notifyNewContactInquiry(ContactInquiry $inquiry): void
    {
        if (Setting::get('notify_email', '1') !== '1') {
            return;
        }

        try {
            $emails = self::getAdminEmails();

            foreach ($emails as $email) {
                Notification::route('mail', $email)
                    ->notify(new NewContactInquiryNotification($inquiry));
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to notify admin about new contact inquiry: '.$e->getMessage());
        }
    }

    /**
     * Resolve the recipient email addresses for admin notifications.
     *
     * @return list<string>
     */
    public static function getAdminEmails(): array
    {
        $custom = Setting::get('admin_notification_email');
        if ($custom) {
            $parsed = array_values(array_filter(array_map('trim', explode(',', $custom))));
            if (! empty($parsed)) {
                return $parsed;
            }
        }

        $adminUsers = User::role('admin')->pluck('email')->filter()->values()->all();
        if (! empty($adminUsers)) {
            return $adminUsers;
        }

        $from = Setting::get('mail_from_address', config('mail.from.address'));

        return $from ? [$from] : [];
    }
}
