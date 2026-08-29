<?php

use App\Livewire\Admin\Billing\BillingIndex;
use App\Mail\PricingRatesMail;
use App\Models\PurchaseRequest;
use App\Models\Setting;
use App\Services\PricingRateService;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

test('admin can view billing and rate sheet management page', function () {
    $admin = createAdmin();

    $this->actingAs($admin)
        ->get(route('admin.billing.index'))
        ->assertOk()
        ->assertSee('Facturación y Tarifario')
        ->assertSee('Rate Sheet & Pricing PDF');
});

test('client can view portal billing and pricing guide page', function () {
    $clientUser = createClient();
    $customer = $clientUser->customer;

    PurchaseRequest::factory()->create([
        'customer_id' => $customer->id,
        'product_name' => 'MacBook Pro M3',
        'unit_price' => 1299.99,
    ]);

    $this->actingAs($clientUser)
        ->get(route('portal.billing.index'))
        ->assertOk()
        ->assertSee('Guía Oficial de Precios y Facturas')
        ->assertSee('MacBook Pro M3');
});

test('admin can update rates from billing index component', function () {
    $admin = createAdmin();
    $rateService = app(PricingRateService::class);
    $current = $rateService->getRates();

    $current['extra_store_fee'] = 25.50;
    $current['box_small_heavy_duty'] = 18.00;

    Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->set('rates', $current)
        ->call('save')
        ->assertHasNoErrors();

    $updated = $rateService->getRates();
    expect((float) $updated['extra_store_fee'])->toBe(25.50)
        ->and((float) $updated['box_small_heavy_duty'])->toBe(18.00);
});

test('admin can send pricing rates PDF via email from billing component', function () {
    Mail::fake();
    Setting::set('admin_notification_email', 'admin@speedingshopper.com');

    $admin = createAdmin();

    Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->set('recipientEmail', 'client@example.com')
        ->set('emailLocale', 'es')
        ->set('customEmailNote', 'Aquí tienes nuestro tarifario.')
        ->call('sendRatesEmail')
        ->assertHasNoErrors()
        ->assertSet('showSendModal', false);

    Mail::assertSent(PricingRatesMail::class, function ($mail) {
        return $mail->hasTo('client@example.com') && $mail->hasBcc('admin@speedingshopper.com');
    });
});

test('unauthenticated users are redirected from billing routes', function () {
    $this->get(route('admin.billing.index'))->assertRedirect(route('login'));
    $this->get(route('portal.billing.index'))->assertRedirect(route('login'));
});
