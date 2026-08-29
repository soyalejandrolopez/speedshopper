<?php

namespace App\Livewire\Admin\Settings;

use App\Concerns\SwalNotifies;
use App\Http\Requests\UpdateSettingsRequest;
use App\Mail\PricingRatesMail;
use App\Mail\TestMail;
use App\Models\Setting;
use App\Providers\MailConfigServiceProvider;
use App\Services\PricingRateService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Testing\Fakes\MailFake;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Settings')]
class SettingsIndex extends Component
{
    use SwalNotifies, WithFileUploads;

    public array $settings = [];

    public array $rates = [];

    public $logo;

    public $favicon;

    public string $testEmail = '';

    public ?string $mailTestStatus = null;

    public string $mailTestMessage = '';

    public bool $showSendModal = false;

    public string $recipientEmail = '';

    public string $emailLocale = 'es';

    public string $customEmailNote = '';

    protected function rules(): array
    {
        return array_merge(
            (new UpdateSettingsRequest)->rules(),
            [
                'logo' => ['nullable', 'image', 'max:2048'],
                'favicon' => ['nullable', 'file', 'max:1024', 'mimes:ico,png,svg,webp,jpeg,jpg'],
                'settings.admin_notification_email' => ['nullable', 'string', 'max:255'],
                'settings.mail_enabled' => ['nullable', 'string'],
                'settings.mail_host' => ['nullable', 'string', 'max:255'],
                'settings.mail_port' => ['nullable', 'string', 'max:5'],
                'settings.mail_username' => ['nullable', 'string', 'max:255'],
                'settings.mail_password' => ['nullable', 'string', 'max:255'],
                'settings.mail_encryption' => ['nullable', 'string', 'in:tls,ssl,'],
                'settings.mail_from_address' => ['nullable', 'string', 'max:255'],
                'settings.mail_from_name' => ['nullable', 'string', 'max:255'],

                // Rate sheet rules
                'rates.shopper_tiers' => ['required', 'array'],
                'rates.shopper_tiers.*.min' => ['required', 'numeric', 'min:0'],
                'rates.shopper_tiers.*.max' => ['nullable', 'numeric', 'min:0'],
                'rates.shopper_tiers.*.percent' => ['required', 'numeric', 'min:0', 'max:100'],
                'rates.shopper_tiers.*.stores' => ['required', 'integer', 'min:1'],
                'rates.shopper_tiers.*.hours' => ['required', 'integer', 'min:1'],
                'rates.extra_store_fee' => ['required', 'numeric', 'min:0'],
                'rates.warehouse_percent' => ['required', 'numeric', 'min:0', 'max:100'],
                'rates.box_small_heavy_duty' => ['required', 'numeric', 'min:0'],
                'rates.box_medium_heavy_duty' => ['required', 'numeric', 'min:0'],
                'rates.box_large_heavy_duty' => ['required', 'numeric', 'min:0'],
                'rates.warehouse_delivery_fee' => ['required', 'numeric', 'min:0'],
                'rates.monthly_storage_fee' => ['required', 'numeric', 'min:0'],
                'rates.notes_es.repackage_notice' => ['nullable', 'string', 'max:1000'],
                'rates.notes_es.storage_notice' => ['nullable', 'string', 'max:1000'],
                'rates.notes_en.repackage_notice' => ['nullable', 'string', 'max:1000'],
                'rates.notes_en.storage_notice' => ['nullable', 'string', 'max:1000'],
            ]
        );
    }

    public function mount(?PricingRateService $rateService = null): void
    {
        $rateService ??= app(PricingRateService::class);

        $this->settings = [
            'company_name' => Setting::get('company_name'),
            'warehouse_address' => Setting::get('warehouse_address'),
            'whatsapp_phone' => Setting::get('whatsapp_phone'),
            'countries_served' => Setting::get('countries_served'),
            'shopper_fee' => Setting::get('shopper_fee'),
            'shopper_fee_is_percent' => Setting::get('shopper_fee_is_percent', '0'),
            'receiving_fee' => Setting::get('receiving_fee'),
            'packing_fee' => Setting::get('packing_fee'),
            'currency' => Setting::get('currency', 'USD'),
            'notify_email' => Setting::get('notify_email', '1'),
            'admin_notification_email' => Setting::get('admin_notification_email', ''),
            'notify_whatsapp' => Setting::get('notify_whatsapp', '0'),
            'whatsapp_api_url' => Setting::get('whatsapp_api_url', ''),
            'whatsapp_api_token' => Setting::get('whatsapp_api_token', ''),
            'theme_color' => Setting::get('theme_color', '#059669'),
            'mail_enabled' => Setting::get('mail_enabled', '0'),
            'mail_host' => Setting::get('mail_host', env('MAIL_HOST', '')),
            'mail_port' => Setting::get('mail_port', env('MAIL_PORT', '587')),
            'mail_username' => Setting::get('mail_username', env('MAIL_USERNAME', '')),
            'mail_password' => Setting::get('mail_password', env('MAIL_PASSWORD', '')),
            'mail_encryption' => Setting::get('mail_encryption', env('MAIL_ENCRYPTION', 'tls')),
            'mail_from_address' => Setting::get('mail_from_address', env('MAIL_FROM_ADDRESS', '')),
            'mail_from_name' => Setting::get('mail_from_name', env('MAIL_FROM_NAME', '')),
        ];

        $this->rates = $rateService->getRates();
    }

    public function save(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $validated = $this->validate();

        foreach ($validated['settings'] as $key => $value) {
            Setting::set($key, $value);
        }

        if (isset($validated['rates'])) {
            app(PricingRateService::class)->saveRates($validated['rates']);
        }

        if ($this->logo) {
            Setting::set('logo_path', $this->logo->store('branding', 'public'));
            $this->logo = null;
        }

        if ($this->favicon) {
            Setting::set('favicon_path', $this->favicon->store('branding', 'public'));
            $this->favicon = null;
        }

        app()->getProvider(MailConfigServiceProvider::class)?->apply();

        $this->swalUpdated();
    }

    public function openSendModal(): void
    {
        $this->resetValidation();
        $this->recipientEmail = '';
        $this->emailLocale = 'es';
        $this->customEmailNote = '';
        $this->showSendModal = true;
    }

    public function closeSendModal(): void
    {
        $this->showSendModal = false;
        $this->resetValidation();
    }

    public function sendRatesEmail(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $this->validate([
            'recipientEmail' => ['required', 'email', 'max:255'],
            'emailLocale' => ['required', 'in:es,en'],
            'customEmailNote' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->applyMailConfigFromForm();

        try {
            $rateService = app(PricingRateService::class);
            $pdf = $rateService->generatePdf($this->emailLocale);
            $pdfOutput = $pdf->output();
            $filename = $this->emailLocale === 'es' ? 'SpeedShopper_Tarifas.pdf' : 'SpeedShopper_Rate_Sheet.pdf';

            $mailable = new PricingRatesMail(
                locale: $this->emailLocale,
                customMessage: $this->customEmailNote,
                pdfOutput: $pdfOutput,
                pdfFilename: $filename
            );

            $adminEmails = array_filter(array_map('trim', explode(',', Setting::get('admin_notification_email', ''))));

            $mail = Mail::to($this->recipientEmail);

            if (! empty($adminEmails)) {
                $mail->bcc($adminEmails);
            }

            $mail->send($mailable);

            $this->showSendModal = false;
            $this->swalSuccess(
                __('Price List PDF sent successfully to').' '.$this->recipientEmail.(! empty($adminEmails) ? ' '.__('(with copy to admin)') : '')
            );
        } catch (\Throwable $e) {
            Log::error('Sending pricing rates mail failed: '.$e->getMessage());
            $this->swalError(__('Could not send email').': '.$e->getMessage());
        }
    }

    public function sendTestEmail(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $this->validate([
            'testEmail' => ['required', 'email', 'max:255'],
        ]);

        if (in_array(strtolower($this->testEmail), ['you@example.com', 'test@example.com'], true)) {
            $this->addError('testEmail', __('Please enter a real email address to receive the test email.'));

            return;
        }

        $this->applyMailConfigFromForm();

        try {
            Mail::to($this->testEmail)->send(new TestMail);

            $this->mailTestStatus = 'sent';
            $this->mailTestMessage = __('Test email sent successfully').' ('.$this->testEmail.')';
        } catch (\Throwable $e) {
            Log::error('SMTP test failed: '.$e->getMessage());

            $this->mailTestStatus = 'error';
            $this->mailTestMessage = __('Test email could not be sent').': '.$e->getMessage();
        }
    }

    protected function applyMailConfigFromForm(): void
    {
        if (app()->bound('mailer') && app('mailer') instanceof MailFake) {
            return;
        }

        $enabled = ($this->settings['mail_enabled'] ?? '0') === '1';
        $host = $this->settings['mail_host'] ?? '';
        $port = $this->settings['mail_port'] ?? '';

        if (! $enabled || ! $host || ! $port) {
            return;
        }

        $username = (string) ($this->settings['mail_username'] ?? '');
        $password = (string) ($this->settings['mail_password'] ?? '');
        $encryption = (string) ($this->settings['mail_encryption'] ?? 'tls');
        $fromAddress = (string) ($this->settings['mail_from_address'] ?? '') ?: $username;

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
                'timeout' => 10,
                'local_domain' => env('MAIL_EHLO_DOMAIN'),
            ],
            'mail.from' => [
                'address' => $fromAddress,
                'name' => (string) ($this->settings['mail_from_name'] ?? '') ?: Setting::get('company_name', config('app.name')),
            ],
        ]);

        app()->forgetInstance('mailer');
    }

    public function removeLogo(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $this->deleteStoredFile(Setting::get('logo_path'));
        Setting::set('logo_path', '');
    }

    public function removeFavicon(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $this->deleteStoredFile(Setting::get('favicon_path'));
        Setting::set('favicon_path', '');
    }

    protected function deleteStoredFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function render()
    {
        return view('livewire.admin.settings.settings-index');
    }
}
