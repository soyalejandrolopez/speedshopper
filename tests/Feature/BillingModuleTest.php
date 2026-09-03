<?php

use App\Enums\CostType;
use App\Enums\RequestStatus;
use App\Livewire\Admin\Billing\BillingIndex;
use App\Livewire\Admin\Rates\RatesIndex;
use App\Mail\InvoiceCreatedMail;
use App\Mail\PricingRatesMail;
use App\Models\CostItem;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Setting;
use App\Services\FinanceService;
use App\Services\InvoicePdfService;
use App\Services\PricingRateService;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

test('admin can view dedicated rate sheet configuration page', function () {
    $admin = createAdmin();

    $this->actingAs($admin)
        ->get(route('admin.rates.index'))
        ->assertOk()
        ->assertSee('Ajuste de Factura');
});

test('admin can view billing invoice management page', function () {
    app()->setLocale('es');
    $admin = createAdmin();

    $this->actingAs($admin)
        ->get(route('admin.billing.index'))
        ->assertOk()
        ->assertSee('Facturación')
        ->assertSee('Nueva Factura');
});

test('admin can create a full invoice with customer, products, and initial payment and sends email in active locale', function () {
    Mail::fake();
    Setting::set('admin_notification_email', 'admin@speedingshopper.com');
    app()->setLocale('es');

    $admin = createAdmin();
    $customer = Customer::factory()->create(['email' => 'customer@example.com']);

    Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->call('openCreateModal')
        ->set('invoiceForm.customer_id', $customer->id)
        ->set('invoiceForm.items', [
            [
                'product_name' => 'Nike Shoes',
                'store' => 'Nike Store',
                'quantity' => 2,
                'unit_price' => 100.0,
            ],
        ])
        ->set('invoiceForm.costs', [
            [
                'type' => 'shopper_fee',
                'description' => 'Comisión Shopper',
                'amount' => 40.0,
            ],
        ])
        ->set('invoiceForm.amount_paid', 140.0)
        ->set('invoiceForm.payment_method', 'zelle')
        ->set('invoiceForm.payment_reference', 'ZELLE-9988')
        ->call('saveInvoice')
        ->assertHasNoErrors()
        ->assertSet('showCreateModal', false);

    $createdRequest = PurchaseRequest::where('customer_id', $customer->id)->latest()->first();
    expect($createdRequest)->not->toBeNull()
        ->and((float) $createdRequest->total_cost)->toBe(240.0);

    $payment = Payment::where('billable_id', $createdRequest->id)->first();
    expect($payment)->not->toBeNull()
        ->and((float) $payment->amount_paid)->toBe(140.0)
        ->and((float) $payment->invoice_total)->toBe(240.0)
        ->and($payment->reference)->toBe('ZELLE-9988');

    Mail::assertSent(InvoiceCreatedMail::class, function ($mail) {
        return $mail->hasTo('customer@example.com')
            && $mail->hasBcc('admin@speedingshopper.com')
            && $mail->locale === 'es'
            && ! empty($mail->pdfOutput);
    });
});

test('admin creating invoice when locale is english sends email and pdf in english', function () {
    Mail::fake();
    Setting::set('admin_notification_email', 'admin@speedingshopper.com');
    app()->setLocale('en');

    $admin = createAdmin(['locale' => 'en']);
    $customer = Customer::factory()->create(['email' => 'client.en@example.com']);

    Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->call('openCreateModal')
        ->set('invoiceForm.customer_id', $customer->id)
        ->set('invoiceForm.items', [
            [
                'product_name' => 'Sony Headphones',
                'store' => 'Best Buy',
                'quantity' => 1,
                'unit_price' => 350.0,
            ],
        ])
        ->call('saveInvoice')
        ->assertHasNoErrors();

    Mail::assertSent(InvoiceCreatedMail::class, function ($mail) {
        return $mail->hasTo('client.en@example.com')
            && $mail->locale === 'en'
            && ! empty($mail->pdfOutput);
    });
});

test('billing component automatically fetches charges and calculates commission from rate sheet', function () {
    $admin = createAdmin();

    $component = Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->call('openCreateModal')
        // Set product: 2 x $250 = $500 total (Falls in Tier 1: $100-$699 @ 20%)
        ->set('invoiceForm.items.0.quantity', 2)
        ->set('invoiceForm.items.0.unit_price', 250.0)
        ->call('syncShopperCommissionRates');

    // Commission should be 20% of 500 = $100
    expect($component->get('productsSubtotal'))->toBe(500.0)
        ->and((float) $component->get('invoiceForm.costs.0.amount'))->toBe(100.0);

    // Quick add box small and extra store
    $component->call('addQuickRateCost', 'box_small')
        ->call('addQuickRateCost', 'extra_store');

    $costs = $component->get('invoiceForm.costs');
    expect($costs)->toHaveCount(3)
        ->and((float) $costs[1]['amount'])->toBe(20.0)
        ->and($costs[1]['description'])->toMatch('/(Visita a Tienda Adicional|Additional Store Visit)/')
        ->and((float) $costs[2]['amount'])->toBe(15.0)
        ->and($costs[2]['description'])->toMatch('/(Caja Small Heavy Duty|Small Heavy Duty Box)/');

    // Total invoiced should be 500 (products) + 100 (shopper) + 15 (box) + 20 (store) = 635.0
    expect((float) $component->get('invoicedTotal'))->toBe(635.0);
});

test('admin can record an abono payment to an invoice from billing index', function () {
    $admin = createAdmin();
    $customer = Customer::factory()->create();
    $request = PurchaseRequest::factory()->create(['customer_id' => $customer->id]);

    CostItem::create([
        'costable_type' => PurchaseRequest::class,
        'costable_id' => $request->id,
        'type' => CostType::ProductCost,
        'amount' => 300.0,
    ]);

    Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->call('openPaymentModal', $request->id)
        ->set('paymentForm.amount_paid', 150.0)
        ->set('paymentForm.payment_method', 'cash')
        ->call('savePayment')
        ->assertHasNoErrors()
        ->assertSet('showPaymentModal', false);

    $payment = Payment::where('billable_id', $request->id)->latest()->first();
    expect($payment)->not->toBeNull()
        ->and((float) $payment->amount_paid)->toBe(150.0);
});

test('admin can edit and update an existing invoice from billing index', function () {
    $admin = createAdmin();
    $customer = Customer::factory()->create();
    $request = PurchaseRequest::factory()->create([
        'customer_id' => $customer->id,
        'product_name' => 'iPad Air',
        'store' => 'Apple',
        'quantity' => 1,
        'unit_price' => 600.0,
    ]);

    CostItem::create([
        'costable_type' => PurchaseRequest::class,
        'costable_id' => $request->id,
        'type' => CostType::ProductCost,
        'description' => 'iPad Air',
        'amount' => 600.0,
    ]);

    Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->call('editInvoice', $request->id)
        ->assertSet('isEditing', true)
        ->assertSet('editingRequestId', $request->id)
        ->set('invoiceForm.items.0.product_name', 'iPad Pro 13"')
        ->set('invoiceForm.items.0.unit_price', 1100.0)
        ->call('incrementQuestion', 'boxes_medium_count')
        ->call('saveInvoice')
        ->assertHasNoErrors()
        ->assertSet('isEditing', false)
        ->assertSet('showCreateForm', false);

    $request->refresh();
    expect($request->product_name)->toBe('iPad Pro 13"')
        ->and((float) $request->total_cost)->toBeGreaterThan(1100.0);
});

test('admin can update rates from dedicated rates index component', function () {
    $admin = createAdmin();
    $rateService = app(PricingRateService::class);
    $current = $rateService->getRates();

    $current['extra_store_fee'] = 25.50;
    $current['box_small_heavy_duty'] = 18.00;

    Livewire::actingAs($admin)
        ->test(RatesIndex::class)
        ->set('rates', $current)
        ->call('save')
        ->assertHasNoErrors();

    $updated = $rateService->getRates();
    expect((float) $updated['extra_store_fee'])->toBe(25.50)
        ->and((float) $updated['box_small_heavy_duty'])->toBe(18.00);
});

test('admin can send pricing rates PDF via email from rates component', function () {
    Mail::fake();
    Setting::set('admin_notification_email', 'admin@speedingshopper.com');

    $admin = createAdmin();

    Livewire::actingAs($admin)
        ->test(RatesIndex::class)
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
        ->assertSee(__('Mis Facturas y Cotizaciones'))
        ->assertSee('MacBook Pro M3');
});

test('billing and rates views render in english when locale is set to en', function () {
    app()->setLocale('en');
    $admin = createAdmin(['locale' => 'en']);
    $customer = Customer::factory()->create();
    PurchaseRequest::factory()->create([
        'customer_id' => $customer->id,
        'product_name' => 'AirPods Max',
        'status' => RequestStatus::Quoted,
    ]);

    $this->actingAs($admin)
        ->withSession(['locale' => 'en'])
        ->get(route('admin.billing.index'))
        ->assertOk()
        ->assertSee('Billing')
        ->assertSee('New Invoice')
        ->assertSee('Balance')
        ->assertSee('Status')
        ->assertSee('Quote sent');

    $this->actingAs($admin)
        ->withSession(['locale' => 'en'])
        ->get(route('admin.rates.index'))
        ->assertOk()
        ->assertSee('Invoice Adjustment')
        ->assertSee('Personal Shopper Tiers');
});

test('online purchase mode automatically enables 15 percent commission, fixed 20 dollar transfer, and excludes product price from invoice total', function () {
    $admin = createAdmin();
    $customer = Customer::factory()->create(['email' => 'online.client@example.com']);

    $component = Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->call('openCreateModal')
        ->call('setServiceType', 'online')
        ->set('invoiceForm.customer_id', $customer->id)
        ->set('invoiceForm.items', [
            [
                'product_name' => 'Amazon Kindle Oasis',
                'store' => 'Amazon US',
                'quantity' => 1,
                'unit_price' => 300.0,
            ],
        ])
        ->call('syncGuidedQuestionsToCosts');

    // Commission: 15% of $300 = $45.00
    // Fixed Box Transfer: $20.00
    // Product Price ($300) should NOT be billed in invoice total
    expect($component->get('serviceType'))->toBe('online')
        ->and($component->get('guidedQuestions.apply_warehouse_commission'))->toBeTrue()
        ->and($component->get('guidedQuestions.warehouse_delivery_count'))->toBe(1)
        ->and($component->get('guidedQuestions.apply_shopper_commission'))->toBeFalse()
        ->and($component->get('productsSubtotal'))->toBe(300.0)
        ->and((float) $component->get('invoicedTotal'))->toBe(65.0); // $45 commission + $20 delivery = $65

    // Save online invoice
    $component->call('saveInvoice')
        ->assertHasNoErrors();

    $createdRequest = PurchaseRequest::where('customer_id', $customer->id)->latest()->first();
    expect($createdRequest)->not->toBeNull()
        ->and((float) $createdRequest->total_cost)->toBe(65.0);

    // Verify cost items: product cost is 0.0 with "(Comprado online - Pagado en internet: $300.00)"
    $productCost = CostItem::where('costable_id', $createdRequest->id)
        ->where('type', CostType::ProductCost)
        ->first();
    expect($productCost)->not->toBeNull()
        ->and((float) $productCost->amount)->toBe(0.0)
        ->and($productCost->description)->toContain('Comprado online');

    $receivingCosts = CostItem::where('costable_id', $createdRequest->id)
        ->where('type', CostType::ReceivingFee)
        ->get();
    expect($receivingCosts)->toHaveCount(2)
        ->and((float) $receivingCosts->sum('amount'))->toBe(65.0);
});

test('repack mode automatically enables fixed 20 dollar transfer, allows box selection with rate sheet prices, and excludes product price from invoice total', function () {
    $admin = createAdmin();
    $customer = Customer::factory()->create(['email' => 'repack.client@example.com']);

    $component = Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->call('openCreateModal')
        ->call('setServiceType', 'repack')
        ->set('invoiceForm.customer_id', $customer->id)
        ->set('invoiceForm.items', [
            [
                'product_name' => 'Shein Clothes Pack',
                'store' => 'Shein',
                'quantity' => 1,
                'unit_price' => 180.0,
            ],
        ])
        ->set('guidedQuestions.boxes_small_count', 1)
        ->set('guidedQuestions.boxes_medium_count', 1)
        ->call('syncGuidedQuestionsToCosts');

    expect($component->get('serviceType'))->toBe('repack')
        ->and($component->get('guidedQuestions.apply_warehouse_commission'))->toBeFalse()
        ->and($component->get('guidedQuestions.apply_shopper_commission'))->toBeFalse()
        ->and($component->get('guidedQuestions.warehouse_delivery_count'))->toBe(1)
        ->and($component->get('productsSubtotal'))->toBe(180.0)
        ->and((float) $component->get('invoicedTotal'))->toBe(55.0); // $15 small + $20 medium + $20 delivery = $55

    // Save repack invoice
    $component->call('saveInvoice')
        ->assertHasNoErrors();

    $createdRequest = PurchaseRequest::where('customer_id', $customer->id)->latest()->first();
    expect($createdRequest)->not->toBeNull()
        ->and((float) $createdRequest->total_cost)->toBe(55.0);

    $packingCosts = CostItem::where('costable_id', $createdRequest->id)
        ->where('type', CostType::PackingFee)
        ->get();
    expect($packingCosts)->toHaveCount(2)
        ->and((float) $packingCosts->sum('amount'))->toBe(35.0);

    $deliveryCost = CostItem::where('costable_id', $createdRequest->id)
        ->where('type', CostType::ReceivingFee)
        ->first();
    expect($deliveryCost)->not->toBeNull()
        ->and((float) $deliveryCost->amount)->toBe(20.0);
});

test('admin can set invoice type as cotizacion, pendiente or pagado and saves correct status', function () {
    $admin = createAdmin();
    $customer = Customer::factory()->create(['email' => 'types.client@example.com']);

    // 1. Cotización
    $componentQuote = Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->call('openCreateModal')
        ->call('setInvoiceType', 'cotizacion')
        ->set('invoiceForm.customer_id', $customer->id)
        ->set('invoiceForm.items', [
            [
                'product_name' => 'Quote Item',
                'store' => 'BestBuy',
                'quantity' => 1,
                'unit_price' => 100.0,
            ],
        ])
        ->call('saveInvoice')
        ->assertHasNoErrors();

    $quoteReq = PurchaseRequest::where('product_name', 'Quote Item')->latest()->first();
    expect($quoteReq)->not->toBeNull()
        ->and($quoteReq->status)->toBe(RequestStatus::Quoted);

    // 2. Pendiente
    $componentPending = Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->call('openCreateModal')
        ->call('setInvoiceType', 'pendiente')
        ->set('invoiceForm.customer_id', $customer->id)
        ->set('invoiceForm.items', [
            [
                'product_name' => 'Pending Item',
                'store' => 'Target',
                'quantity' => 1,
                'unit_price' => 150.0,
            ],
        ])
        ->call('saveInvoice')
        ->assertHasNoErrors();

    $pendingReq = PurchaseRequest::where('product_name', 'Pending Item')->latest()->first();
    expect($pendingReq)->not->toBeNull()
        ->and($pendingReq->status)->toBe(RequestStatus::AwaitingPayment);

    // 3. Pagado
    $componentPaid = Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->call('openCreateModal')
        ->call('setInvoiceType', 'pagado')
        ->set('invoiceForm.customer_id', $customer->id)
        ->set('invoiceForm.items', [
            [
                'product_name' => 'Paid Item',
                'store' => 'Apple',
                'quantity' => 1,
                'unit_price' => 200.0,
            ],
        ])
        ->call('saveInvoice')
        ->assertHasNoErrors();

    $paidReq = PurchaseRequest::where('product_name', 'Paid Item')->latest()->first();
    expect($paidReq)->not->toBeNull()
        ->and($paidReq->status)->toBe(RequestStatus::Purchased);
});

test('invoice pdf renders payment methods and instructions for pending and quote status', function () {
    $customer = Customer::factory()->create();
    $purchaseRequest = PurchaseRequest::factory()->create([
        'customer_id' => $customer->id,
        'product_name' => 'AirPods Pro',
        'status' => RequestStatus::Quoted,
        'unit_price' => 249.0,
        'quantity' => 1,
    ]);

    $pdfService = app(InvoicePdfService::class);
    $pdf = $pdfService->generatePdf($purchaseRequest, 'es');
    $html = $pdf->output();

    expect($html)->not->toBeEmpty();

    // Directly test the Blade view rendering
    $viewHtml = view('pdf.invoice-pdf', [
        'request' => $purchaseRequest,
        'locale' => 'es',
        'companyName' => 'Speed Shopper',
        'companyEmail' => 'info@speedshopper.com',
        'warehouseAddress' => 'Miami, FL',
        'whatsappPhone' => '+1 (555) 000-0000',
        'logoBase64' => null,
        'qrDataUri' => null,
        'paymentImageBase64' => null,
        'totalCost' => 249.0,
        'paidAmount' => 0.0,
        'balance' => 249.0,
        'generatedAt' => now(),
    ])->render();

    expect($viewHtml)->toContain('Gomez.Lilibeth1977@gmail.com')
        ->and($viewHtml)->toContain('@speedingshopper')
        ->and($viewHtml)->toContain('Zelle')
        ->and($viewHtml)->toContain('PayPal')
        ->and($viewHtml)->toContain('Cotización');

    // Test print view rendering
    app()->setLocale('es');
    $printHtml = view('print.quote', [
        'request' => $purchaseRequest->load(['customer', 'costItems']),
    ])->render();

    expect($printHtml)->toContain('Gomez.Lilibeth1977@gmail.com')
        ->and($printHtml)->toContain('@speedingshopper')
        ->and($printHtml)->toContain('MÉTODOS DE PAGO DISPONIBLES');

    // Test paid invoice rendering includes 7835 Wood Hollow Dr Baytown Tx 77521
    $paidRequest = PurchaseRequest::factory()->create([
        'customer_id' => $customer->id,
        'status' => RequestStatus::Purchased,
        'unit_price' => 200.0,
        'quantity' => 1,
    ]);

    $paidPdfHtml = view('pdf.invoice-pdf', [
        'request' => $paidRequest,
        'locale' => 'es',
        'companyName' => 'Speed Shopper',
        'companyEmail' => 'info@speedshopper.com',
        'warehouseAddress' => '7835 Wood Hollow Dr, Baytown, TX 77521, USA',
        'whatsappPhone' => '+1 (555) 000-0000',
        'logoBase64' => null,
        'qrDataUri' => null,
        'paymentImageBase64' => null,
        'totalCost' => 200.0,
        'paidAmount' => 200.0,
        'balance' => 0.0,
        'generatedAt' => now(),
    ])->render();

    expect($paidPdfHtml)->toContain('7835 Wood Hollow Dr Baytown Tx 77521')
        ->and($paidPdfHtml)->toContain('FACTURA PAGADA EN SU TOTALIDAD');

    $paidPrintHtml = view('print.quote', [
        'request' => $paidRequest->load(['customer', 'costItems']),
    ])->render();

    expect($paidPrintHtml)->toContain('7835 Wood Hollow Dr Baytown Tx 77521')
        ->and($paidPrintHtml)->toContain('FACTURA PAGADA EN SU TOTALIDAD');

    $paidMailHtml = view('mail.invoice-created-mail', [
        'purchaseRequest' => $paidRequest,
        'locale' => 'es',
        'companyName' => 'Speed Shopper',
        'totalCost' => 200.0,
        'paidAmount' => 200.0,
        'balance' => 0.0,
        'isUpdate' => false,
    ])->render();

    expect($paidMailHtml)->toContain('7835 Wood Hollow Dr Baytown Tx 77521');
});

test('updating an invoice automatically sends updated email to customer and admin', function () {
    Mail::fake();
    Setting::set('admin_notification_email', 'admin@speedingshopper.com');
    app()->setLocale('es');

    $admin = createAdmin();
    $customer = Customer::factory()->create(['email' => 'client.update@example.com']);
    $request = PurchaseRequest::factory()->create([
        'customer_id' => $customer->id,
        'status' => RequestStatus::Quoted,
        'product_name' => 'Original Product',
        'unit_price' => 100.0,
        'quantity' => 1,
    ]);

    Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->call('editInvoice', $request->id)
        ->set('invoiceForm.items.0.product_name', 'Updated Product Name')
        ->set('invoiceForm.items.0.unit_price', 150.0)
        ->call('setInvoiceType', 'pendiente')
        ->call('saveInvoice')
        ->assertHasNoErrors();

    Mail::assertSent(InvoiceCreatedMail::class, function ($mail) {
        return $mail->hasTo('client.update@example.com')
            && $mail->hasBcc('admin@speedingshopper.com')
            && $mail->isUpdate === true
            && str_contains($mail->envelope()->subject, '[Actualización] Factura Pendiente')
            && ! empty($mail->pdfOutput);
    });
});

test('billing automatically links budget value to unit price and calculates earnings', function () {
    $admin = createAdmin();
    $customer = Customer::factory()->create(['name' => 'Roberto Gómez']);

    // Create a request with unit_price (e.g. from registration form budget)
    $request = PurchaseRequest::factory()->create([
        'customer_id' => $customer->id,
        'status' => RequestStatus::New,
        'product_name' => 'Nike Air Force 1',
        'unit_price' => 250.0,
        'quantity' => 1,
    ]);

    CostItem::create([
        'costable_type' => PurchaseRequest::class,
        'costable_id' => $request->id,
        'type' => CostType::ProductCost,
        'description' => 'Valor de Productos / Presupuesto Cliente',
        'amount' => 250.0,
    ]);

    CostItem::create([
        'costable_type' => PurchaseRequest::class,
        'costable_id' => $request->id,
        'type' => CostType::ShopperFee,
        'description' => 'Comisión Personal Shopper (20%)',
        'amount' => 50.0,
    ]);

    app()->setLocale('es');

    Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->call('editInvoice', $request->id)
        ->assertSet('invoiceForm.items.0.unit_price', 250.0)
        ->assertSet('productsSubtotal', 250.0)
        ->assertSet('invoicedEarnings', 50.0)
        ->assertSee('Ganancia por esta venta')
        ->assertSee('50.00');
});

test('unauthenticated users are redirected from billing and rates routes', function () {
    $this->get(route('admin.billing.index'))->assertRedirect(route('login'));
    $this->get(route('admin.rates.index'))->assertRedirect(route('login'));
    $this->get(route('portal.billing.index'))->assertRedirect(route('login'));
});

test('service_definitions returns the 3 main services: Personal Shopper, Comprar Online, and Reempaque', function () {
    app()->setLocale('es');
    $services = service_definitions();

    expect($services)->toHaveKeys(['personal_shopper', 'online_shopping', 'repack'])
        ->and($services['personal_shopper']['title'])->toBe('Personal Shopper')
        ->and($services['personal_shopper']['subtitle'])->toContain('20% - 15%')
        ->and($services['online_shopping']['title'])->toBe('Comprar Online')
        ->and($services['online_shopping']['subtitle'])->toContain('15%')
        ->and($services['online_shopping']['subtitle'])->toContain('$20')
        ->and($services['repack']['title'])->toBe('Reempaque')
        ->and($services['repack']['subtitle'])->toContain('$15')
        ->and($services['repack']['subtitle'])->toContain('$20')
        ->and($services['repack']['subtitle'])->toContain('$25');
});

test('billing index correctly computes online shopping and repack service invoices', function () {
    $admin = createAdmin();

    $customer = Customer::create([
        'name' => 'Ana Morales',
        'email' => 'ana@example.com',
    ]);

    // Test Comprar Online mode: $1000 online product -> 15% comm ($150) + $20 delivery = $170 total
    Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->call('openCreateForm')
        ->call('setServiceType', 'online')
        ->set('invoiceForm.customer_id', $customer->id)
        ->set('invoiceForm.items.0.product_name', 'Laptop Dell')
        ->set('invoiceForm.items.0.unit_price', 1000.0)
        ->call('syncGuidedQuestionsToCosts')
        ->assertSet('serviceType', 'online')
        ->assertSet('productsSubtotal', 1000.0)
        ->assertSet('invoicedTotal', 170.0) // $150 (15%) + $20 delivery
        ->assertSet('invoicedEarnings', 170.0);

    // Test Reempaque mode: 1 Small ($15), 1 Med ($20), 1 Large ($25) + $20 delivery = $80 total
    Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->call('openCreateForm')
        ->call('setServiceType', 'repack')
        ->set('invoiceForm.customer_id', $customer->id)
        ->set('invoiceForm.items.0.product_name', 'Paquetes de Ropa')
        ->set('invoiceForm.items.0.unit_price', 0.0)
        ->set('guidedQuestions.boxes_small_count', 1)
        ->set('guidedQuestions.boxes_medium_count', 1)
        ->set('guidedQuestions.boxes_large_count', 1)
        ->set('guidedQuestions.warehouse_delivery_count', 1)
        ->call('syncGuidedQuestionsToCosts')
        ->assertSet('serviceType', 'repack')
        ->assertSet('invoicedTotal', 80.0) // $15 + $20 + $25 + $20 = $80
        ->assertSet('invoicedEarnings', 80.0);
});

test('billing index supports multi-service selection combining personal shopper and repack', function () {
    $admin = createAdmin();

    $customer = Customer::create([
        'name' => 'Carlos Multi',
        'email' => 'carlos.multi@example.com',
    ]);

    $component = Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->call('openCreateForm')
        ->assertSet('selectedServices', ['personal_shopper'])
        ->call('toggleService', 'repack')
        ->assertSet('selectedServices', ['personal_shopper', 'repack'])
        ->set('invoiceForm.customer_id', $customer->id)
        ->set('invoiceForm.items.0.product_name', 'Zapatos Nike')
        ->set('invoiceForm.items.0.unit_price', 100.0)
        ->set('guidedQuestions.boxes_small_count', 2) // 2 * $15 = $30
        ->set('guidedQuestions.warehouse_delivery_count', 1) // $20
        ->call('syncGuidedQuestionsToCosts');

    // Subtotal products: $100
    // Personal Shopper commission (20%): $20
    // Small boxes: $30
    // Delivery fee: $20
    // Total invoiced: $100 + $20 + $30 + $20 = $170
    // Earnings: $20 + $30 + $20 = $70
    expect($component->get('invoicedTotal'))->toBe(170.0)
        ->and($component->get('invoicedEarnings'))->toBe(70.0);

    $component->call('saveInvoice')
        ->assertHasNoErrors();

    $request = PurchaseRequest::where('customer_id', $customer->id)->first();
    expect($request)->not->toBeNull()
        ->and($request->services)->toBe(['personal_shopper', 'repack'])
        ->and((float) $request->total_cost)->toBe(170.0);

    // Edit invoice and ensure services are correctly preserved
    Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->call('editInvoice', $request->id)
        ->assertSet('selectedServices', ['personal_shopper', 'repack'])
        ->assertSet('invoicedTotal', 170.0);
});

test('editInvoice does not preselect Paid / Full payment received by default', function () {
    $admin = createAdmin();
    $customer = Customer::factory()->create();
    $request = PurchaseRequest::factory()->create([
        'customer_id' => $customer->id,
        'status' => RequestStatus::New,
        'unit_price' => 500.0,
    ]);

    Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->call('editInvoice', $request->id)
        ->assertSet('invoiceType', 'cotizacion')
        ->assertSet('invoiceForm.invoice_type', 'cotizacion')
        ->assertNotSet('invoiceType', 'pagado');
});

test('purchased invoice reflects zero pending balance and full total cobrado', function () {
    $admin = createAdmin();
    $customer = Customer::factory()->create();
    $request = PurchaseRequest::factory()->create([
        'customer_id' => $customer->id,
        'status' => RequestStatus::Purchased,
        'unit_price' => 350.0,
        'quantity' => 1,
    ]);
    CostItem::create([
        'costable_type' => PurchaseRequest::class,
        'costable_id' => $request->id,
        'type' => CostType::ProductCost,
        'description' => 'Test Item',
        'amount' => 350.0,
    ]);

    $finance = app(FinanceService::class);
    expect($finance->getRequestsTotalBalanceDue())->toBe(0.0)
        ->and($finance->getRequestsTotalCollected())->toBe(350.0)
        ->and($customer->fresh()->balance_due)->toBe(0.0);

    Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->assertSee('OK');
});

test('unpurchased invoice reflects balance in pending balance', function () {
    $admin = createAdmin();
    $customer = Customer::factory()->create();
    $request = PurchaseRequest::factory()->create([
        'customer_id' => $customer->id,
        'status' => RequestStatus::Quoted,
        'unit_price' => 200.0,
        'quantity' => 1,
    ]);
    CostItem::create([
        'costable_type' => PurchaseRequest::class,
        'costable_id' => $request->id,
        'type' => CostType::ProductCost,
        'description' => 'Unpaid Item',
        'amount' => 200.0,
    ]);

    $finance = app(FinanceService::class);
    expect($finance->getRequestsTotalBalanceDue())->toBe(200.0)
        ->and($finance->getRequestsTotalCollected())->toBe(0.0)
        ->and($customer->fresh()->balance_due)->toBe(200.0);
});
