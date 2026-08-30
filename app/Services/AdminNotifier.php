<?php

namespace App\Services;

use App\Models\ContactInquiry;
use App\Models\PurchaseRequest;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\ClientPurchaseRequestConfirmationNotification;
use App\Notifications\NewContactInquiryNotification;
use App\Notifications\NewPurchaseRequestNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AdminNotifier
{
    /**
     * Notify administrators and customer about a newly created purchase request with attached PDF.
     */
    public static function notifyNewPurchaseRequest(PurchaseRequest $request): void
    {
        if (Setting::get('notify_email', '1') !== '1') {
            return;
        }

        try {
            $pdfContent = null;
            try {
                $pdf = app(InvoicePdfService::class)->generatePdf($request);
                $pdfContent = $pdf->output();
            } catch (\Throwable $e) {
                Log::warning('Failed to generate PDF for purchase request notification: '.$e->getMessage());
            }

            // 1. Notify Administrators
            $emails = self::getAdminEmails();
            foreach ($emails as $email) {
                Notification::route('mail', $email)
                    ->notify(new NewPurchaseRequestNotification($request, $pdfContent));
            }

            // 2. Notify Customer
            $customerEmail = $request->customer?->email;
            if ($customerEmail && filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                Notification::route('mail', $customerEmail)
                    ->notify(new ClientPurchaseRequestConfirmationNotification($request, $pdfContent));
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to notify about new purchase request: '.$e->getMessage());
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
