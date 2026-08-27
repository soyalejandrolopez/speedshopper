<?php

use App\Livewire\Admin\Settings\SettingsIndex;
use App\Mail\TestMail;
use App\Models\Setting;
use App\Providers\MailConfigServiceProvider;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

it('applies the smtp configuration when enabled', function () {
    seedRoles();

    Setting::set('mail_enabled', '1');
    Setting::set('mail_host', 'smtp.example.com');
    Setting::set('mail_port', '587');
    Setting::set('mail_username', 'no-reply@example.com');
    Setting::set('mail_encryption', 'tls');
    Setting::set('mail_from_name', 'Mi Empresa');

    app()->getProvider(MailConfigServiceProvider::class)->apply();

    expect(config('mail.default'))->toBe('smtp')
        ->and(config('mail.mailers.smtp.host'))->toBe('smtp.example.com')
        ->and(config('mail.mailers.smtp.port'))->toBe(587)
        ->and(config('mail.mailers.smtp.username'))->toBe('no-reply@example.com')
        ->and(config('mail.from.name'))->toBe('Mi Empresa')
        ->and(config('mail.from.address'))->toBe('no-reply@example.com');
});

it('keeps the default mailer when smtp is disabled', function () {
    seedRoles();

    app()->getProvider(MailConfigServiceProvider::class)->apply();

    expect(config('mail.default'))->toBe('array');
});

it('shows the mail section in settings', function () {
    $this->actingAs(createAdmin());

    Livewire::test(SettingsIndex::class)
        ->assertOk()
        ->assertSee(__('Mail / SMTP'))
        ->assertSee(__('Send Test Email'));
});

it('sends a test email', function () {
    $this->actingAs(createAdmin());
    Mail::fake();

    Livewire::test(SettingsIndex::class)
        ->set('testEmail', 'owner@example.com')
        ->call('sendTestEmail')
        ->assertHasNoErrors();

    Mail::assertSent(TestMail::class, fn ($mail) => $mail->hasTo('owner@example.com'));
});

it('validates the test email address', function () {
    $this->actingAs(createAdmin());
    Mail::fake();

    Livewire::test(SettingsIndex::class)
        ->set('testEmail', 'not-an-email')
        ->call('sendTestEmail')
        ->assertHasErrors('testEmail');

    Mail::assertNothingSent();
});
