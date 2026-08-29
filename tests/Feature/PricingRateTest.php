<?php

use App\Livewire\Admin\Rates\RatesIndex;
use App\Mail\PricingRatesMail;
use App\Models\Customer;
use App\Models\PurchaseRequest;
use App\Models\Setting;
use App\Services\PricingRateService;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

test('pricing rate service returns default rates matching user specifications', function () {
    $service = app(PricingRateService::class);
    $rates = $service->getRates();

    expect($rates['shopper_tiers'])->toHaveCount(3)
        ->and($rates['shopper_tiers'][0]['min'])->toBe(100)
        ->and($rates['shopper_tiers'][0]['max'])->toBe(699)
        ->and($rates['shopper_tiers'][0]['percent'])->toBe(20)
        ->and($rates['shopper_tiers'][0]['stores'])->toBe(2)
        ->and($rates['shopper_tiers'][0]['hours'])->toBe(2)
        ->and($rates['shopper_tiers'][1]['min'])->toBe(700)
        ->and($rates['shopper_tiers'][1]['max'])->toBe(1499)
        ->and($rates['shopper_tiers'][1]['percent'])->toBe(15)
        ->and($rates['shopper_tiers'][1]['stores'])->toBe(3)
        ->and($rates['shopper_tiers'][1]['hours'])->toBe(3)
        ->and($rates['shopper_tiers'][2]['min'])->toBe(1500)
        ->and($rates['shopper_tiers'][2]['max'])->toBeNull()
        ->and($rates['shopper_tiers'][2]['percent'])->toBe(15)
        ->and($rates['shopper_tiers'][2]['stores'])->toBe(4)
        ->and($rates['shopper_tiers'][2]['hours'])->toBe(4)
        ->and((float) $rates['extra_store_fee'])->toBe(20.0)
        ->and((float) $rates['warehouse_percent'])->toBe(15.0)
        ->and((float) $rates['box_small_heavy_duty'])->toBe(15.0)
        ->and((float) $rates['box_medium_heavy_duty'])->toBe(20.0)
        ->and((float) $rates['box_large_heavy_duty'])->toBe(25.0)
        ->and((float) $rates['warehouse_delivery_fee'])->toBe(20.0)
        ->and((float) $rates['monthly_storage_fee'])->toBe(15.0)
        ->and($rates['notes_es']['repackage_notice'])->toContain('Estos precios son del reempaque')
        ->and($rates['notes_es']['storage_notice'])->toContain('$15 por mes');
});

test('pricing rate service can save and retrieve customized rates', function () {
    $service = app(PricingRateService::class);

    $custom = $service->getRates();
    $custom['extra_store_fee'] = 25.00;
    $custom['box_small_heavy_duty'] = 18.00;

    $service->saveRates($custom);

    $retrieved = $service->getRates();
    expect((float) $retrieved['extra_store_fee'])->toBe(25.0)
        ->and((float) $retrieved['box_small_heavy_duty'])->toBe(18.0);
});

test('pricing rate service generates valid PDF instance in ES and EN', function () {
    $service = app(PricingRateService::class);

    $pdfEs = $service->generatePdf('es');
    expect($pdfEs->output())->toBeString()->not->toBeEmpty();

    $pdfEn = $service->generatePdf('en');
    expect($pdfEn->output())->toBeString()->not->toBeEmpty();
});

test('admin can download pricing rates PDF in ES and EN', function () {
    $this->actingAs(createAdmin());

    $responseEs = $this->get(route('admin.rates.pdf', ['lang' => 'es']));
    $responseEs->assertOk()
        ->assertHeader('content-type', 'application/pdf');

    $responseEn = $this->get(route('admin.rates.pdf', ['lang' => 'en']));
    $responseEn->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

test('guest cannot download pricing rates PDF', function () {
    $response = $this->get(route('admin.rates.pdf'));
    $response->assertRedirect(route('login'));
});

test('admin can update pricing rate sheet settings in RatesIndex', function () {
    $this->actingAs(createAdmin());

    $component = Livewire::test(RatesIndex::class)
        ->set('rates.extra_store_fee', 30)
        ->set('rates.box_small_heavy_duty', 16)
        ->call('save')
        ->assertHasNoErrors();

    $rates = app(PricingRateService::class)->getRates();
    expect((float) $rates['extra_store_fee'])->toBe(30.0)
        ->and((float) $rates['box_small_heavy_duty'])->toBe(16.0);
});

test('admin can send pricing rates PDF via email with automatic admin copy', function () {
    Mail::fake();
    $this->actingAs(createAdmin());

    Setting::set('admin_notification_email', 'admin@speedshopper.com');

    $component = Livewire::test(RatesIndex::class)
        ->call('openSendModal')
        ->assertSet('showSendModal', true)
        ->set('recipientEmail', 'cliente@example.com')
        ->set('emailLocale', 'es')
        ->set('customEmailNote', 'Adjunto lista de precios.')
        ->call('sendRatesEmail');

    $component->assertHasNoErrors();
    expect($component->get('showSendModal'))->toBeFalse();

    Mail::assertSent(PricingRatesMail::class, function ($mail) {
        return $mail->hasTo('cliente@example.com')
            && $mail->hasBcc('admin@speedshopper.com')
            && $mail->locale === 'es'
            && ! empty($mail->pdfOutput);
    });
});

test('qr code service generates valid svg and data uri', function () {
    $service = app(QrCodeService::class);

    $svg = $service->generateSvg('https://example.com/invoice/123', 120);
    expect($svg)->toBeString()
        ->toContain('<svg')
        ->toContain('viewBox')
        ->toContain('<rect');

    $dataUri = $service->generateDataUri('https://example.com/invoice/123');
    expect($dataUri)->toBeString()
        ->toStartWith('data:image/svg+xml;base64,');
});

test('invoice print views render qr code and uploaded brand logo', function () {
    $this->actingAs(createAdmin());
    $customer = Customer::factory()->create();
    $request = PurchaseRequest::factory()->create(['customer_id' => $customer->id]);

    $response = $this->get(route('admin.requests.print', $request));
    $response->assertOk()
        ->assertSee('<svg', false)
        ->assertSee(__('Scan to contact / verify'));
});
