<?php

use App\Livewire\Admin\Reports\ReportsIndex;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Setting;
use App\Models\Shipment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

it('renders the reports page', function () {
    $this->actingAs(createAdmin());

    Livewire::test(ReportsIndex::class)
        ->assertOk()
        ->assertSee(__('Total Facturado'))
        ->assertSee(__('Generador de Reportes'))
        ->assertSee(__('Download PDF'))
        ->assertSee(__('Export Data (CSV)'));
});

it('exports customers as CSV', function () {
    $this->actingAs(createAdmin());

    Livewire::test(ReportsIndex::class)
        ->call('exportCustomers')
        ->assertFileDownloaded();
});

it('exports the financial report in csv, excel and pdf', function () {
    $this->actingAs(createAdmin());

    Livewire::test(ReportsIndex::class)
        ->call('exportReportCsv')
        ->assertFileDownloaded();

    Livewire::test(ReportsIndex::class)
        ->call('exportReportExcel')
        ->assertFileDownloaded();

    Livewire::test(ReportsIndex::class)
        ->call('exportReportPdf')
        ->assertFileDownloaded();
});

it('allows filtering the report by custom dates', function () {
    $admin = createAdmin();
    $customer = Customer::factory()->create(['user_id' => $admin->id]);
    Payment::factory()->create([
        'customer_id' => $customer->id,
        'invoice_total' => 100,
        'amount_paid' => 100,
        'created_at' => now()->subDays(10),
    ]);

    $this->actingAs($admin);

    Livewire::test(ReportsIndex::class)
        ->set('period', 'custom')
        ->set('from', now()->subDays(20)->format('Y-m-d'))
        ->set('to', now()->format('Y-m-d'))
        ->assertOk()
        ->assertSee('100.00');
});

it('embeds the custom logo in the excel report', function () {
    seedRoles();
    Storage::disk('public')->put('branding/logo.png', base64_decode(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
    ));
    Setting::set('logo_path', 'branding/logo.png');

    $component = new ReportsIndex;
    $data = (fn () => $this->reportData(now()->startOfMonth(), now()->endOfMonth()))->call($component);
    $spreadsheet = (fn () => $this->buildExcel($data))->call($component);

    $writer = new Xlsx($spreadsheet);
    ob_start();
    $writer->save('php://output');
    $xlsx = ob_get_clean();

    expect(str_starts_with($xlsx, 'PK'))->toBeTrue()
        ->and(str_contains($xlsx, 'drawing1.xml'))->toBeTrue()
        ->and(str_contains($xlsx, '.png'))->toBeTrue();
});

it('embeds the custom logo in the pdf report', function () {
    seedRoles();
    Storage::disk('public')->put('branding/logo.png', base64_decode(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
    ));
    Setting::set('logo_path', 'branding/logo.png');

    $component = new ReportsIndex;
    $data = (fn () => $this->reportData(now()->startOfMonth(), now()->endOfMonth()))->call($component);
    $pdf = Pdf::loadView('reports.pdf', $data)->output();

    expect(str_contains($pdf, '/Image'))->toBeTrue();
});

it('renders the printable quote for a request', function () {
    $admin = createAdmin();
    $customer = Customer::factory()->create(['user_id' => $admin->id]);
    $request = PurchaseRequest::factory()->create(['customer_id' => $customer->id]);

    $this->actingAs($admin)
        ->get(route('admin.requests.print', $request))
        ->assertOk()
        ->assertSee(__('Quote'))
        ->assertSee($request->number);
});

it('renders the printable receipt for a shipment', function () {
    $admin = createAdmin();
    $customer = Customer::factory()->create(['user_id' => $admin->id]);
    $package = Package::factory()->create(['customer_id' => $customer->id]);
    $shipment = Shipment::factory()->create(['customer_id' => $customer->id]);
    $shipment->packages()->attach($package);

    $this->actingAs($admin)
        ->get(route('admin.shipments.print', $shipment))
        ->assertOk()
        ->assertSee(__('Receipt'))
        ->assertSee($shipment->number);
});

it('renders the portal dashboard with the new layout', function () {
    $user = createClient();
    $customer = Customer::where('user_id', $user->id)->first();
    PurchaseRequest::factory()->create(['customer_id' => $customer->id]);
    Shipment::factory()->create(['customer_id' => $customer->id]);

    $this->actingAs($user)
        ->get('/portal')
        ->assertOk()
        ->assertSee(__('Outstanding Balance'))
        ->assertSee(__('Your orders'))
        ->assertSee(__('Your boxes'));
});
