<?php

namespace App\Livewire\Admin\Settings;

use App\Http\Requests\UpdateSettingsRequest;
use App\Mail\TestMail;
use App\Models\Setting;
use App\Providers\MailConfigServiceProvider;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Settings')]
class SettingsIndex extends Component
{
    use WithFileUploads;

    public array $settings = [];

    public $logo;

    public $favicon;

    public string $testEmail = '';

    protected function rules(): array
    {
        return array_merge(
            (new UpdateSettingsRequest)->rules(),
            [
                'logo' => ['nullable', 'image', 'max:2048'],
                'favicon' => ['nullable', 'file', 'max:1024', 'mimes:ico,png,svg,webp,jpeg,jpg'],
                'settings.mail_enabled' => ['nullable', 'string'],
                'settings.mail_host' => ['nullable', 'string', 'max:255'],
                'settings.mail_port' => ['nullable', 'string', 'max:5'],
                'settings.mail_username' => ['nullable', 'string', 'max:255'],
                'settings.mail_password' => ['nullable', 'string', 'max:255'],
                'settings.mail_encryption' => ['nullable', 'string', 'in:tls,ssl,'],
                'settings.mail_from_address' => ['nullable', 'string', 'max:255'],
                'settings.mail_from_name' => ['nullable', 'string', 'max:255'],
            ]
        );
    }

    public function mount(): void
    {
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
    }

    public function save(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $validated = $this->validate();

        foreach ($validated['settings'] as $key => $value) {
            Setting::set($key, $value);
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

        session()->flash('success', __('Settings saved successfully.'));
    }

    public function sendTestEmail(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $this->validate([
            'testEmail' => ['required', 'email', 'max:255'],
        ]);

        $this->applyMailConfigFromForm();

        try {
            Mail::to($this->testEmail)->send(new TestMail);

            session()->flash('success', __('Test email sent successfully.'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('SMTP test failed: '.$e->getMessage());

            session()->flash('error', __('Test email could not be sent') . ': '.$e->getMessage());
        }
    }

    protected function applyMailConfigFromForm(): void
    {
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
