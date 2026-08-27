<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;

class MailConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->apply();
    }

    /**
     * Override the runtime mail configuration with the values saved in Settings.
     * Safe to call again after settings change (used by the test-email flow).
     */
    public function apply(): void
    {
        try {
            if (Setting::get('mail_enabled', '0') !== '1') {
                return;
            }

            $host = Setting::get('mail_host');
            $port = Setting::get('mail_port');

            if (! $host || ! $port) {
                return;
            }

            $username = (string) Setting::get('mail_username', '');
            $password = (string) Setting::get('mail_password', '');
            $encryption = (string) Setting::get('mail_encryption', 'tls');

            $fromAddress = Setting::get('mail_from_address', '') ?: $username;

            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp' => [
                    'transport' => 'smtp',
                    'url' => env('MAIL_URL'),
                    'host' => $host,
                    'port' => (int) $port,
                    'username' => $username,
                    'password' => $password,
                    'encryption' => $encryption ?: null,
                    'timeout' => null,
                    'local_domain' => env('MAIL_EHLO_DOMAIN'),
                ],
                'mail.from' => [
                    'address' => $fromAddress,
                    'name' => Setting::get('mail_from_name', Setting::get('company_name', config('app.name'))),
                ],
            ]);
        } catch (\Throwable) {
            // Settings table may not be available yet (fresh install / migrations).
        }
    }
}
