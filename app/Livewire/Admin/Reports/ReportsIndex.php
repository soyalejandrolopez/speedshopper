<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Setting;
use App\Models\Shipment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.app')]
#[Title('Reports')]
class ReportsIndex extends Component
{
    public string $period = 'monthly';

    public string $month = '';

    public int $year = 0;

    public string $from = '';

    public string $to = '';

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
        $this->year = (int) now()->format('Y');
        $this->from = now()->startOfMonth()->format('Y-m-d');
        $this->to = now()->format('Y-m-d');
    }

    public function render()
    {
        [$start, $end] = $this->range();

        $payments = Payment::query()
            ->with(['customer', 'billable.costItems'])
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $allPayments = Payment::query()
            ->with(['billable.costItems'])
            ->get();

        $totalInvoicedEarnings = (float) $allPayments->sum(fn (Payment $p) => $p->invoiced_service_earnings);
        $totalCollectedEarnings = (float) $allPayments->sum(fn (Payment $p) => $p->service_earnings);
        $balanceDueEarnings = max(0.0, $totalInvoicedEarnings - $totalCollectedEarnings);

        $thisMonthPayments = Payment::query()
            ->with(['billable.costItems'])
            ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->get();
        $revenueThisMonthEarnings = (float) $thisMonthPayments->sum(fn (Payment $p) => $p->service_earnings);

        $balanceByCustomer = Customer::query()
            ->with(['payments.billable.costItems'])
            ->whereNull('deleted_at')
            ->get()
            ->map(function (Customer $customer) {
                $serviceBalance = (float) $customer->payments->sum(fn (Payment $p) => $p->service_balance_due);

                return [
                    'name' => $customer->name,
                    'balance' => $serviceBalance,
                ];
            })
            ->filter(fn (array $row) => $row['balance'] > 0.005)
            ->sortByDesc('balance')
            ->take(10)
            ->values();

        $periodInvoicedEarnings = (float) $payments->sum(fn (Payment $p) => $p->invoiced_service_earnings);
        $periodCollectedEarnings = (float) $payments->sum(fn (Payment $p) => $p->service_earnings);
        $periodBalanceEarnings = max(0.0, $periodInvoicedEarnings - $periodCollectedEarnings);

        return view('livewire.admin.reports.reports-index', [
            'totalInvoiced' => $totalInvoicedEarnings,
            'totalCollected' => $totalCollectedEarnings,
            'balanceDue' => $balanceDueEarnings,
            'revenueThisMonth' => $revenueThisMonthEarnings,
            'grossTotalInvoiced' => (float) Payment::sum('invoice_total'),
            'grossTotalCollected' => (float) Payment::sum('amount_paid'),
            'customersCount' => Customer::count(),
            'requestsCount' => PurchaseRequest::count(),
            'packagesCount' => Package::count(),
            'shipmentsCount' => Shipment::count(),
            'paymentsCount' => Payment::count(),

            'reportPeriod' => [
                'start' => $start,
                'end' => $end,
                'label' => $this->periodLabel($start, $end),
                'invoiced' => $periodInvoicedEarnings,
                'collected' => $periodCollectedEarnings,
                'balance' => $periodBalanceEarnings,
                'gross_invoiced' => (float) $payments->sum('invoice_total'),
                'gross_collected' => (float) $payments->sum('amount_paid'),
                'newCustomers' => Customer::whereBetween('created_at', [$start, $end])->count(),
                'requests' => PurchaseRequest::whereBetween('created_at', [$start, $end])->count(),
                'packages' => Package::whereBetween('created_at', [$start, $end])->count(),
                'shipments' => Shipment::whereBetween('created_at', [$start, $end])->count(),
                'payments' => $payments,
                'revenue' => $this->revenueBuckets($payments),
            ],
            'balanceByCustomer' => $balanceByCustomer,
            'maxCustomerBalance' => max($balanceByCustomer->max('balance') ?? 1, 1),
        ]);
    }

    public function exportReportCsv(): StreamedResponse
    {
        [$start, $end] = $this->range();
        $data = $this->reportData($start, $end);
        $name = $this->fileName('report');

        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');

            fputcsv($out, [Setting::get('company_name', config('app.name'))]);
            fputcsv($out, [$data['period']['label']]);
            fputcsv($out, []);
            fputcsv($out, ['Metric', 'Value']);
            foreach ($data['summary'] as $label => $value) {
                fputcsv($out, [$label, $value]);
            }
            fputcsv($out, []);
            fputcsv($out, ['Payments']);
            fputcsv($out, ['Number', 'Customer', 'Method', 'Date', 'Invoice Total', 'Amount Paid', 'Balance']);
            foreach ($data['payments'] as $p) {
                fputcsv($out, [
                    $p['number'],
                    $p['customer'],
                    $p['method'],
                    $p['date'],
                    number_format($p['invoice_total'], 2),
                    number_format($p['amount_paid'], 2),
                    number_format($p['balance'], 2),
                ]);
            }

            fclose($out);
        }, $name, ['Content-Type' => 'text/csv']);
    }

    public function exportReportExcel(): StreamedResponse
    {
        [$start, $end] = $this->range();
        $data = $this->reportData($start, $end);

        $spreadsheet = $this->buildExcel($data);

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $this->fileName('report', 'xlsx'), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportReportPdf(): StreamedResponse
    {
        [$start, $end] = $this->range();
        $data = $this->reportData($start, $end);

        $pdf = Pdf::loadView('reports.pdf', $data)->setPaper('letter');

        return response()->streamDownload(fn () => print ($pdf->output()), $this->fileName('report', 'pdf'), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /** Build a real .xlsx workbook with the company logo embedded. */
    protected function buildExcel(array $data): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(__('Financial Report'));

        $money = '"$"#,##0.00';
        $color600 = 'FF'.str_replace('#', '', $data['colors']['600']);
        $color800 = 'FF'.str_replace('#', '', $data['colors']['800']);

        $path = Setting::get('logo_path');
        if ($path && Storage::disk('public')->exists($path)) {
            try {
                $drawing = new Drawing;
                $drawing->setName('Logo');
                $drawing->setDescription($data['company']);
                $drawing->setPath(Storage::disk('public')->path($path));
                $drawing->setHeight(50);
                $drawing->setCoordinates('A1');
                $drawing->setWorksheet($sheet);
            } catch (\Throwable) {
                // Image format not supported by Excel; continue without it.
            }
        }

        $sheet->setCellValue('B1', $data['company']);
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB($color800);
        $sheet->setCellValue('B2', $data['address']);
        $sheet->getStyle('B2')->getFont()->setSize(10)->getColor()->setARGB('FF6B7280');

        $sheet->setCellValue('A4', __('Financial Report'));
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB($color600);
        $sheet->setCellValue('A5', $data['period']['label']);
        $sheet->getStyle('A5')->getFont()->setSize(11)->getColor()->setARGB('FF4B5563');
        $sheet->setCellValue('B5', __('Generated').': '.$data['generatedAt']);
        $sheet->getStyle('B5')->getFont()->setSize(10)->getColor()->setARGB('FF9CA3AF');

        $row = 7;
        $sheet->setCellValue("A{$row}", __('Ganancia Facturada (Servicios)'));
        $sheet->setCellValue("B{$row}", $data['period']['invoiced']);
        $sheet->setCellValue("C{$row}", __('New Customers'));
        $sheet->setCellValue("D{$row}", $data['period']['newCustomers']);
        $sheet->getStyle("B{$row}")->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue("A{$row}", __('Ganancia Cobrada (Servicios)'));
        $sheet->setCellValue("B{$row}", $data['period']['collected']);
        $sheet->setCellValue("C{$row}", __('Requests'));
        $sheet->setCellValue("D{$row}", $data['period']['requests']);
        $sheet->getStyle("B{$row}")->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue("A{$row}", __('Saldo Pendiente de Servicios'));
        $sheet->setCellValue("B{$row}", $data['period']['balance']);
        $sheet->setCellValue("C{$row}", __('Packages'));
        $sheet->setCellValue("D{$row}", $data['period']['packages']);
        $sheet->getStyle("B{$row}")->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue("C{$row}", __('Shipments'));
        $sheet->setCellValue("D{$row}", $data['period']['shipments']);
        $sheet->getStyle('A7:B9')->getNumberFormat()->setFormatCode($money);

        $row += 2;
        $sheet->setCellValue("A{$row}", __('Ganancia por Período'));
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12)->getColor()->setARGB($color800);
        $row++;

        $revenueHeader = $row;
        $sheet->fromArray([__('Period'), __('Ganancia Cobrada')], null, "A{$row}");
        $this->styleTableHeader($sheet, "A{$row}:B{$row}");
        $row++;

        foreach ($data['revenue'] as $item) {
            $sheet->setCellValue("A{$row}", $item['label']);
            $sheet->setCellValue("B{$row}", $item['total']);
            $row++;
        }
        $sheet->getStyle('B'.($revenueHeader + 1).':B'.($row - 1))->getNumberFormat()->setFormatCode($money);

        $row += 2;
        $sheet->setCellValue("A{$row}", __('Payments').' ('.count($data['payments']).')');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12)->getColor()->setARGB($color800);
        $row++;

        $paymentsHeader = $row;
        $sheet->fromArray([__('Number'), __('Customer'), __('Method'), __('Date'), __('Ganancia Facturada'), __('Ganancia Cobrada'), __('Saldo')], null, "A{$row}");
        $this->styleTableHeader($sheet, "A{$row}:G{$row}");
        $row++;

        foreach ($data['payments'] as $p) {
            $sheet->setCellValue("A{$row}", $p['number']);
            $sheet->setCellValue("B{$row}", $p['customer']);
            $sheet->setCellValue("C{$row}", $p['method']);
            $sheet->setCellValue("D{$row}", $p['date']);
            $sheet->setCellValue("E{$row}", $p['invoice_total']);
            $sheet->setCellValue("F{$row}", $p['amount_paid']);
            $sheet->setCellValue("G{$row}", $p['balance']);
            $row++;
        }
        $sheet->getStyle('E'.($paymentsHeader + 1).':G'.($row - 1))->getNumberFormat()->setFormatCode($money);
        $sheet->getStyle('A'.($paymentsHeader + 1).':G'.($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        foreach (['A' => 20, 'B' => 28, 'C' => 15, 'D' => 16, 'E' => 18, 'F' => 18, 'G' => 14] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        return $spreadsheet;
    }

    protected function styleTableHeader(Worksheet $sheet, string $range): void
    {
        $color600 = 'FF'.str_replace('#', '', theme_color_ramp(theme_color())['600']);

        $sheet->getStyle($range)->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color600);
    }

    /** @return array{logo: ?string, company: string, period: array, summary: array, payments: array, revenue: array} */
    protected function reportData(Carbon $start, Carbon $end): array
    {
        $payments = Payment::query()
            ->with(['customer', 'billable.costItems'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get();

        $invoiced = (float) $payments->sum(fn (Payment $p) => $p->invoiced_service_earnings);
        $collected = (float) $payments->sum(fn (Payment $p) => $p->service_earnings);
        $balance = max(0.0, $invoiced - $collected);

        return [
            'logo' => $this->logoDataUri(),
            'company' => Setting::get('company_name', config('app.name')),
            'address' => Setting::get('warehouse_address', ''),
            'generatedAt' => now()->format('Y-m-d H:i'),
            'colors' => theme_color_ramp(theme_color()),
            'period' => [
                'start' => $start,
                'end' => $end,
                'label' => $this->periodLabel($start, $end),
                'invoiced' => $invoiced,
                'collected' => $collected,
                'balance' => $balance,
                'gross_invoiced' => (float) $payments->sum('invoice_total'),
                'gross_collected' => (float) $payments->sum('amount_paid'),
                'newCustomers' => Customer::whereBetween('created_at', [$start, $end])->count(),
                'requests' => PurchaseRequest::whereBetween('created_at', [$start, $end])->count(),
                'packages' => Package::whereBetween('created_at', [$start, $end])->count(),
                'shipments' => Shipment::whereBetween('created_at', [$start, $end])->count(),
            ],
            'summary' => [
                __('Ganancia Facturada (Servicios)') => $invoiced,
                __('Ganancia Cobrada (Servicios)') => $collected,
                __('Saldo Pendiente de Servicios') => $balance,
                __('New Customers') => Customer::whereBetween('created_at', [$start, $end])->count(),
                __('Requests') => PurchaseRequest::whereBetween('created_at', [$start, $end])->count(),
                __('Packages') => Package::whereBetween('created_at', [$start, $end])->count(),
                __('Shipments') => Shipment::whereBetween('created_at', [$start, $end])->count(),
            ],
            'payments' => $payments->map(fn (Payment $p) => [
                'number' => $p->number,
                'customer' => $p->customer?->name ?? '—',
                'method' => $p->payment_method->label(),
                'date' => $p->paid_at?->format('Y-m-d') ?? $p->created_at?->format('Y-m-d') ?? '',
                'invoice_total' => (float) $p->invoiced_service_earnings,
                'amount_paid' => (float) $p->service_earnings,
                'balance' => (float) $p->service_balance_due,
                'gross_total' => (float) $p->invoice_total,
                'gross_paid' => (float) $p->amount_paid,
            ])->all(),
            'revenue' => $this->revenueBuckets($payments),
        ];
    }

    /** @return array<int, array{label: string, total: float}> */
    protected function revenueBuckets($payments): array
    {
        $groupBy = $this->period === 'yearly' ? 'Y-m' : 'Y-m-d';
        $buckets = $payments->groupBy(fn (Payment $p) => $p->created_at?->format($groupBy));

        $rows = [];
        foreach ($buckets as $key => $group) {
            $rows[] = [
                'label' => $this->period === 'yearly'
                    ? Carbon::createFromFormat('Y-m', $key)->translatedFormat('M Y')
                    : Carbon::createFromFormat('Y-m-d', $key)->translatedFormat('M d'),
                'total' => (float) $group->sum(fn (Payment $p) => $p->service_earnings),
            ];
        }

        usort($rows, fn ($a, $b) => strcmp($a['label'], $b['label']));

        return $rows;
    }

    /** @return array{Carbon, Carbon} */
    protected function range(): array
    {
        return match ($this->period) {
            'yearly' => [
                Carbon::create($this->year, 1, 1)->startOfDay(),
                Carbon::create($this->year, 12, 31)->endOfDay(),
            ],
            'custom' => [
                Carbon::parse($this->from ?: now()->startOfMonth())->startOfDay(),
                Carbon::parse($this->to ?: now())->endOfDay(),
            ],
            default => [
                Carbon::createFromFormat('Y-m', $this->month ?: now()->format('Y-m'))->startOfMonth(),
                Carbon::createFromFormat('Y-m', $this->month ?: now()->format('Y-m'))->endOfMonth(),
            ],
        };
    }

    protected function periodLabel(Carbon $start, Carbon $end): string
    {
        if ($start->isSameDay($end->copy()->startOfMonth()) && $end->isSameDay($end->copy()->endOfMonth())) {
            return __('Month of').' '.$start->translatedFormat('F Y');
        }

        if ($start->isSameDay($start->copy()->startOfYear()) && $end->isSameDay($start->copy()->endOfYear())) {
            return __('Year').' '.$start->format('Y');
        }

        return $start->format('Y-m-d').' → '.$end->format('Y-m-d');
    }

    protected function fileName(string $slug, string $ext = 'csv'): string
    {
        return $slug.'-'.now()->format('Y-m-d').'.'.$ext;
    }

    protected function logoDataUri(): ?string
    {
        $path = Setting::get('logo_path');

        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        $mime = Storage::disk('public')->mimeType($path) ?? 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode(Storage::disk('public')->get($path));
    }

    public function exportCustomers(): StreamedResponse
    {
        $rows = Customer::withCount('purchaseRequests')->orderBy('number')->get()->map(fn (Customer $c) => [
            $c->number,
            $c->name,
            $c->email,
            $c->whatsapp,
            country_name($c->country),
            $c->address,
            $c->city,
            $c->country,
            number_format($c->balance_due, 2),
            $c->registered_at?->format('Y-m-d') ?? '',
            $c->purchase_requests_count,
        ]);

        return $this->csv(__('Customers'), [
            'Number', 'Name', 'Email', 'WhatsApp', 'Country', 'Address', 'City', 'Country Code', 'Balance Due', 'Registered At', 'Requests',
        ], $rows);
    }

    public function exportRequests(): StreamedResponse
    {
        $rows = PurchaseRequest::with('customer')->orderBy('number')->get()->map(fn (PurchaseRequest $r) => [
            $r->number,
            $r->customer?->name ?? '',
            $r->product_name,
            $r->store ?? '',
            $r->product_url ?? '',
            $r->description ?? '',
            $r->size_color ?? '',
            $r->quantity,
            number_format((float) $r->unit_price, 2),
            number_format((float) $r->discount_found, 2),
            number_format($r->total_cost, 2),
            $r->status->label(),
            $r->created_at?->format('Y-m-d H:i') ?? '',
        ]);

        return $this->csv(__('Purchase Requests'), [
            'Number', 'Customer', 'Product', 'Store', 'URL', 'Description', 'Size/Color', 'Quantity', 'Unit Price', 'Discount', 'Total Cost', 'Status', 'Created At',
        ], $rows);
    }

    public function exportPackages(): StreamedResponse
    {
        $rows = Package::with('customer')->orderBy('number')->get()->map(fn (Package $p) => [
            $p->number,
            $p->customer?->name ?? '',
            $p->store ?? '',
            $p->purchase_request_id,
            $p->original_tracking ?? '',
            $p->received_at?->format('Y-m-d') ?? '',
            number_format((float) $p->weight_lb, 2),
            $p->location ?? '',
            $p->status->label(),
        ]);

        return $this->csv(__('Packages'), [
            'Number', 'Customer', 'Store', 'Request ID', 'Tracking', 'Received At', 'Weight (lb)', 'Location', 'Status',
        ], $rows);
    }

    public function exportShipments(): StreamedResponse
    {
        $rows = Shipment::with('customer')->orderBy('number')->get()->map(fn (Shipment $s) => [
            $s->number,
            $s->customer?->name ?? '',
            $s->carrier ?? '',
            country_name($s->destination_country),
            number_format((float) $s->final_weight_lb, 2),
            $s->dimensions ?? '',
            $s->international_tracking ?? '',
            number_format((float) $s->shipping_cost, 2),
            number_format($s->total_cost, 2),
            $s->status->label(),
            $s->shipped_at?->format('Y-m-d') ?? '',
            $s->delivered_at?->format('Y-m-d') ?? '',
        ]);

        return $this->csv(__('Shipments'), [
            'Number', 'Customer', 'Carrier', 'Destination', 'Weight (lb)', 'Dimensions', 'International Tracking', 'Shipping Cost', 'Total Cost', 'Status', 'Shipped At', 'Delivered At',
        ], $rows);
    }

    public function exportPayments(): StreamedResponse
    {
        $rows = Payment::with(['customer', 'billable.costItems'])->orderBy('number')->get()->map(fn (Payment $p) => [
            $p->number,
            $p->customer?->name ?? '',
            number_format((float) $p->invoiced_service_earnings, 2),
            number_format((float) $p->service_earnings, 2),
            number_format((float) $p->service_balance_due, 2),
            number_format((float) $p->invoice_total, 2),
            number_format((float) $p->amount_paid, 2),
            $p->payment_method->label(),
            $p->paid_at?->format('Y-m-d H:i') ?? '',
        ]);

        return $this->csv(__('Payments'), [
            'Number', 'Customer', 'Ganancia Facturada (Servicios)', 'Ganancia Cobrada (Servicios)', 'Saldo Pendiente', 'Total Facturado Bruto', 'Total Cobrado Bruto', 'Method', 'Paid At',
        ], $rows);
    }

    protected function csv(string $name, array $headers, Collection $rows): StreamedResponse
    {
        $filename = strtolower(str_replace([' ', '/'], '-', $name)).'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($headers, $rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            foreach ($rows as $row) {
                fputcsv($out, is_array($row) ? $row : $row->toArray());
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
