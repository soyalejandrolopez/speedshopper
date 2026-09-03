<?php

namespace App\Livewire\Admin\Reports;

use App\Enums\CostType;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Setting;
use App\Models\Shipment;
use App\Services\FinanceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
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

        $requests = PurchaseRequest::query()
            ->billed()
            ->with(['customer', 'costItems'])
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $payments = Payment::query()
            ->with(['customer', 'billable.costItems'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])
                    ->orWhereBetween('paid_at', [$start, $end]);
            })
            ->get();

        $allRequests = PurchaseRequest::billed()->with(['customer', 'costItems'])->get();

        $financeMetrics = app(FinanceService::class)->getMetrics();
        $totalInvoiced = $financeMetrics['total_invoiced'];
        $totalEarnings = $financeMetrics['total_earnings'];
        $totalCollected = $financeMetrics['total_collected'];
        $totalBalanceDue = $financeMetrics['total_balance_due'];

        $thisMonthCollected = (float) Payment::query()
            ->where(function ($q) {
                $q->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->orWhere(function ($q2) {
                        $q2->whereNull('paid_at')->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                    });
            })
            ->sum('amount_paid');

        $balanceByCustomer = Customer::query()
            ->whereNull('deleted_at')
            ->get()
            ->map(fn (Customer $customer) => [
                'name' => $customer->name,
                'balance' => (float) $customer->balance_due,
            ])
            ->filter(fn (array $row) => $row['balance'] > 0.005)
            ->sortByDesc('balance')
            ->take(10)
            ->values();

        $requestsInvoiced = (float) $requests->sum(function (PurchaseRequest $r) {
            $costs = (float) $r->costItems->sum('amount');
            if ($costs > 0) {
                return $costs;
            }
            if ($r->unit_price) {
                return (float) ($r->unit_price * max(1, $r->quantity));
            }

            return 0.0;
        });

        $requestsEarnings = (float) $requests->sum(function (PurchaseRequest $r) {
            return (float) $r->costItems->where('type', '!=', CostType::ProductCost)->sum('amount');
        });

        // Standalone direct invoices in period
        $standaloneInvoices = Payment::where(function ($q) {
            $q->whereNull('billable_type')->orWhere('billable_type', '!=', PurchaseRequest::class);
        })
            ->where('invoice_total', '>', 0)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])
                    ->orWhereBetween('paid_at', [$start, $end]);
            })
            ->with('customer')
            ->get();

        $standaloneInvoiced = (float) $standaloneInvoices->sum('invoice_total');
        $standaloneEarnings = (float) $standaloneInvoices->sum('invoiced_service_earnings');

        $periodInvoiced = $requestsInvoiced + $standaloneInvoiced;
        $periodEarnings = $requestsEarnings + $standaloneEarnings;
        $periodCollected = (float) $payments->sum('amount_paid');
        $periodBalance = max(0.0, $periodInvoiced - $periodCollected);

        $requestsList = $requests->map(function (PurchaseRequest $r) {
            $totalCost = (float) $r->total_cost;
            if ($totalCost == 0.0 && $r->unit_price) {
                $totalCost = (float) $r->unit_price * max(1, $r->quantity);
            }
            $earnings = (float) $r->costItems->where('type', '!=', CostType::ProductCost)->sum('amount');

            return [
                'id' => $r->id,
                'number' => $r->number,
                'customer' => $r->customer?->name ?? '—',
                'customer_id' => $r->customer?->id,
                'date' => $r->created_at?->format('Y-m-d') ?? '',
                'details' => $r->product_name.($r->quantity > 1 ? " (x{$r->quantity})" : ''),
                'status' => $r->status?->label() ?? ucfirst($r->status?->value ?? '—'),
                'status_color' => $r->status?->color() ?? 'gray',
                'invoice_total' => $totalCost,
                'service_profit' => $earnings,
            ];
        });

        $standaloneInvoicesList = $standaloneInvoices->map(function (Payment $p) {
            return [
                'id' => null,
                'number' => $p->number,
                'customer' => $p->customer?->name ?? '—',
                'customer_id' => $p->customer_id,
                'date' => $p->paid_at?->format('Y-m-d') ?? $p->created_at?->format('Y-m-d') ?? '',
                'details' => $p->notes ?: __('Factura Directa'),
                'status' => $p->balance_due <= 0.005 ? __('Pagado') : __('Pendiente'),
                'status_color' => $p->balance_due <= 0.005 ? 'green' : 'amber',
                'invoice_total' => (float) $p->invoice_total,
                'service_profit' => (float) $p->invoiced_service_earnings,
            ];
        });

        $invoicesList = $requestsList->concat($standaloneInvoicesList)->sortByDesc('date')->values();

        $customerPaymentsList = $payments->groupBy('customer_id')->map(function ($custPayments) {
            $first = $custPayments->first();
            $customer = $first->customer;
            $totalPaid = (float) $custPayments->sum('amount_paid');
            $methods = $custPayments->map(fn (Payment $p) => $p->payment_method?->label() ?? '—')->filter()->unique()->join(', ');
            $latestDate = $custPayments->max(fn (Payment $p) => $p->paid_at ?? $p->created_at);

            return [
                'customer' => $customer?->name ?? '—',
                'customer_id' => $customer?->id,
                'customer_whatsapp' => $customer?->whatsapp,
                'payments_count' => $custPayments->count(),
                'methods' => $methods ?: '—',
                'latest_date' => $latestDate ? Carbon::parse($latestDate)->format('Y-m-d') : '—',
                'total_paid' => $totalPaid,
                'payments' => $custPayments->map(fn (Payment $p) => [
                    'number' => $p->number,
                    'amount' => (float) $p->amount_paid,
                    'method' => $p->payment_method?->label() ?? '—',
                    'date' => $p->paid_at?->format('Y-m-d') ?? $p->created_at?->format('Y-m-d') ?? '',
                    'reference' => $p->reference,
                ])->values()->all(),
            ];
        })->sortByDesc('total_paid')->values();

        return view('livewire.admin.reports.reports-index', [
            'totalInvoiced' => $totalInvoiced,
            'totalEarnings' => $totalEarnings,
            'totalCollected' => $totalCollected,
            'balanceDue' => $totalBalanceDue,
            'revenueThisMonth' => $thisMonthCollected,
            'customersCount' => Customer::count(),
            'requestsCount' => $requests->count(),
            'packagesCount' => Package::whereBetween('created_at', [$start, $end])->count(),
            'shipmentsCount' => Shipment::whereBetween('created_at', [$start, $end])->count(),
            'paymentsCount' => Payment::count(),

            'reportPeriod' => [
                'start' => $start,
                'end' => $end,
                'label' => $this->periodLabel($start, $end),
                'invoiced' => $periodInvoiced,
                'earnings' => $periodEarnings,
                'collected' => $periodCollected,
                'balance' => $periodBalance,
                'newCustomers' => Customer::whereBetween('created_at', [$start, $end])->count(),
                'requests' => $requests->count(),
                'packages' => Package::whereBetween('created_at', [$start, $end])->count(),
                'shipments' => Shipment::whereBetween('created_at', [$start, $end])->count(),
                'payments' => $payments,
                'revenue' => $this->revenueBuckets($payments),
                'invoicesList' => $invoicesList,
                'customerPaymentsList' => $customerPaymentsList,
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
                fputcsv($out, [$label, is_numeric($value) ? number_format($value, 2) : $value]);
            }
            // TABLA 1: LO FACTURADO
            fputcsv($out, [__('Lo Facturado (Facturas del Período)')]);
            fputcsv($out, [__('Number'), __('Customer'), __('Date'), __('Method'), __('Total Facturado'), __('Ganancia Servicios')]);
            foreach ($data['invoices'] as $inv) {
                fputcsv($out, [
                    $inv['number'],
                    $inv['customer'],
                    $inv['date'],
                    $inv['method'] ?? '—',
                    number_format($inv['invoice_total'], 2),
                    number_format($inv['service_profit'], 2),
                ]);
            }
            fputcsv($out, ['', '', '', __('Total Facturado'), number_format($data['period']['invoiced'], 2)]);
            fputcsv($out, []);

            // TABLA 2: PAGOS POR CLIENTE
            fputcsv($out, [__('Pagos por Cliente (Cobrado en el Período)')]);
            fputcsv($out, [__('Customer'), __('Number of Payments'), __('Method'), __('Last Payment Date'), __('Total Pagado')]);
            foreach ($data['customerPayments'] as $cp) {
                fputcsv($out, [
                    $cp['customer'],
                    $cp['payments_count'],
                    $cp['methods'],
                    $cp['latest_date'],
                    number_format($cp['total_paid'], 2),
                ]);
            }
            fputcsv($out, ['', '', '', __('Total Pagado por Clientes'), number_format($data['period']['collected'], 2)]);

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
        $sheet->setCellValue("A{$row}", __('Total Facturado'));
        $sheet->setCellValue("B{$row}", $data['period']['invoiced']);
        $sheet->setCellValue("C{$row}", __('New Customers'));
        $sheet->setCellValue("D{$row}", $data['period']['newCustomers']);
        $sheet->getStyle("B{$row}")->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue("A{$row}", __('Ganancia Servicios'));
        $sheet->setCellValue("B{$row}", $data['period']['earnings']);
        $sheet->setCellValue("C{$row}", __('Requests'));
        $sheet->setCellValue("D{$row}", $data['period']['requests']);
        $sheet->getStyle("B{$row}")->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue("A{$row}", __('Total Cobrado'));
        $sheet->setCellValue("B{$row}", $data['period']['collected']);
        $sheet->setCellValue("C{$row}", __('Packages'));
        $sheet->setCellValue("D{$row}", $data['period']['packages']);
        $sheet->getStyle("B{$row}")->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue("A{$row}", __('Saldo por Cobrar'));
        $sheet->setCellValue("B{$row}", $data['period']['balance']);
        $sheet->setCellValue("C{$row}", __('Shipments'));
        $sheet->setCellValue("D{$row}", $data['period']['shipments']);
        $sheet->getStyle("B{$row}")->getFont()->setBold(true);
        $sheet->getStyle('A7:B10')->getNumberFormat()->setFormatCode($money);

        $row += 2;
        $sheet->setCellValue("A{$row}", __('Ingresos Cobrados por Período'));
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12)->getColor()->setARGB($color800);
        $row++;

        $revenueHeader = $row;
        $sheet->fromArray([__('Period'), __('Cobrado')], null, "A{$row}");
        $this->styleTableHeader($sheet, "A{$row}:B{$row}");
        $row++;

        foreach ($data['revenue'] as $item) {
            $sheet->setCellValue("A{$row}", $item['label']);
            $sheet->setCellValue("B{$row}", $item['total']);
            $row++;
        }
        $sheet->getStyle('B'.($revenueHeader + 1).':B'.($row - 1))->getNumberFormat()->setFormatCode($money);

        $row += 2;
        // TABLA 1: LO FACTURADO
        $sheet->setCellValue("A{$row}", __('Lo Facturado (Facturas del Período)').' ('.count($data['invoices']).')');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12)->getColor()->setARGB($color800);
        $row++;

        $invoicesHeader = $row;
        $sheet->fromArray([__('Number'), __('Customer'), __('Date'), __('Method'), __('Total Facturado')], null, "A{$row}");
        $this->styleTableHeader($sheet, "A{$row}:E{$row}");
        $row++;

        foreach ($data['invoices'] as $inv) {
            $sheet->setCellValue("A{$row}", $inv['number']);
            $sheet->setCellValue("B{$row}", $inv['customer']);
            $sheet->setCellValue("C{$row}", $inv['date']);
            $sheet->setCellValue("D{$row}", $inv['method'] ?? '—');
            $sheet->setCellValue("E{$row}", $inv['invoice_total']);
            $row++;
        }
        $sheet->setCellValue("D{$row}", __('Total Facturado'));
        $sheet->setCellValue("E{$row}", $data['period']['invoiced']);
        $sheet->getStyle("D{$row}:E{$row}")->getFont()->setBold(true);
        $sheet->getStyle('E'.($invoicesHeader + 1).":E{$row}")->getNumberFormat()->setFormatCode($money);
        $sheet->getStyle('A'.($invoicesHeader + 1).":E{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $row += 3;
        // TABLA 2: PAGOS POR CLIENTE
        $sheet->setCellValue("A{$row}", __('Pagos por Cliente (Cobrado en el Período)').' ('.count($data['customerPayments']).')');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12)->getColor()->setARGB($color800);
        $row++;

        $paymentsHeader = $row;
        $sheet->fromArray([__('Customer'), __('Number of Payments'), __('Method'), __('Last Payment Date'), __('Total Pagado')], null, "A{$row}");
        $this->styleTableHeader($sheet, "A{$row}:E{$row}");
        $row++;

        foreach ($data['customerPayments'] as $cp) {
            $sheet->setCellValue("A{$row}", $cp['customer']);
            $sheet->setCellValue("B{$row}", $cp['payments_count']);
            $sheet->setCellValue("C{$row}", $cp['methods']);
            $sheet->setCellValue("D{$row}", $cp['latest_date']);
            $sheet->setCellValue("E{$row}", $cp['total_paid']);
            $row++;
        }
        $sheet->setCellValue("D{$row}", __('Total Pagado por Clientes'));
        $sheet->setCellValue("E{$row}", $data['period']['collected']);
        $sheet->getStyle("D{$row}:E{$row}")->getFont()->setBold(true);
        $sheet->getStyle('E'.($paymentsHeader + 1).":E{$row}")->getNumberFormat()->setFormatCode($money);
        $sheet->getStyle('A'.($paymentsHeader + 1).":E{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        foreach (['A' => 20, 'B' => 28, 'C' => 16, 'D' => 28, 'E' => 18] as $col => $width) {
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

    /** @return array{logo: ?string, company: string, period: array, summary: array, payments: array, revenue: array, balanceByCustomer: array} */
    protected function reportData(Carbon $start, Carbon $end): array
    {
        $requests = PurchaseRequest::query()
            ->billed()
            ->with(['customer', 'costItems'])
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $payments = Payment::query()
            ->with(['customer', 'billable.costItems'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])
                    ->orWhereBetween('paid_at', [$start, $end]);
            })
            ->orderBy('created_at')
            ->get();

        $requestsInvoiced = (float) $requests->sum(function (PurchaseRequest $r) {
            $costs = (float) $r->costItems->sum('amount');
            if ($costs > 0) {
                return $costs;
            }
            if ($r->unit_price) {
                return (float) ($r->unit_price * max(1, $r->quantity));
            }

            return 0.0;
        });

        $requestsEarnings = (float) $requests->sum(function (PurchaseRequest $r) {
            return (float) $r->costItems->where('type', '!=', CostType::ProductCost)->sum('amount');
        });

        // Standalone direct invoices in period
        $standaloneInvoices = Payment::where(function ($q) {
            $q->whereNull('billable_type')->orWhere('billable_type', '!=', PurchaseRequest::class);
        })
            ->where('invoice_total', '>', 0)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])
                    ->orWhereBetween('paid_at', [$start, $end]);
            })
            ->with('customer')
            ->get();

        $standaloneInvoiced = (float) $standaloneInvoices->sum('invoice_total');
        $standaloneEarnings = (float) $standaloneInvoices->sum('invoiced_service_earnings');

        $invoiced = $requestsInvoiced + $standaloneInvoiced;
        $earnings = $requestsEarnings + $standaloneEarnings;
        $collected = (float) $payments->sum('amount_paid');
        $balance = max(0.0, $invoiced - $collected);

        $requestIds = $requests->pluck('id')->toArray();
        $paymentsByRequest = Payment::where('billable_type', PurchaseRequest::class)
            ->whereIn('billable_id', $requestIds)
            ->get()
            ->groupBy('billable_id');

        $requestsList = $requests->map(function (PurchaseRequest $r) use ($paymentsByRequest) {
            $totalCost = (float) $r->total_cost;
            if ($totalCost == 0.0 && $r->unit_price) {
                $totalCost = (float) $r->unit_price * max(1, $r->quantity);
            }
            $earnings = (float) $r->costItems->where('type', '!=', CostType::ProductCost)->sum('amount');
            $requestPayments = $paymentsByRequest->get($r->id) ?? collect();
            $paid = (float) $requestPayments->sum('amount_paid');
            $balance = max(0.0, $totalCost - $paid);
            $lastPaymentMethod = $requestPayments->last()?->payment_method?->label() ?? '—';

            return [
                'number' => $r->number,
                'customer' => $r->customer?->name ?? '—',
                'method' => $lastPaymentMethod,
                'date' => $r->created_at?->format('Y-m-d') ?? '',
                'details' => $r->product_name.($r->quantity > 1 ? " (x{$r->quantity})" : ''),
                'status' => $r->status?->label() ?? ucfirst($r->status?->value ?? '—'),
                'invoice_total' => $totalCost,
                'service_profit' => $earnings,
                'amount_paid' => $paid,
                'balance' => $balance,
            ];
        });

        $standaloneInvoicesList = $standaloneInvoices->map(function (Payment $p) {
            return [
                'number' => $p->number,
                'customer' => $p->customer?->name ?? '—',
                'method' => $p->payment_method?->label() ?? '—',
                'date' => $p->paid_at?->format('Y-m-d') ?? $p->created_at?->format('Y-m-d') ?? '',
                'details' => $p->notes ?: __('Factura Directa'),
                'status' => $p->balance_due <= 0.005 ? __('Pagado') : __('Pendiente'),
                'invoice_total' => (float) $p->invoice_total,
                'service_profit' => (float) $p->invoiced_service_earnings,
                'amount_paid' => (float) $p->amount_paid,
                'balance' => (float) $p->balance_due,
            ];
        });

        $invoicesList = $requestsList->concat($standaloneInvoicesList)->sortByDesc('date')->values();

        $linkedPaymentIds = Payment::where('billable_type', PurchaseRequest::class)
            ->whereIn('billable_id', $requestIds)
            ->pluck('id')
            ->toArray();

        $standalonePayments = $payments->whereNotIn('id', $linkedPaymentIds)->map(function (Payment $p) {
            return [
                'number' => $p->number,
                'customer' => $p->customer?->name ?? '—',
                'method' => $p->payment_method?->label() ?? '—',
                'date' => $p->paid_at?->format('Y-m-d') ?? $p->created_at?->format('Y-m-d') ?? '',
                'invoice_total' => (float) $p->invoice_total,
                'service_profit' => (float) $p->invoiced_service_earnings,
                'amount_paid' => (float) $p->amount_paid,
                'balance' => (float) $p->balance_due,
            ];
        });

        $allRecords = $invoicesList->concat($standalonePayments)->sortByDesc('date')->values()->all();

        $balanceByCustomer = Customer::query()
            ->whereNull('deleted_at')
            ->get()
            ->map(fn (Customer $customer) => [
                'name' => $customer->name,
                'balance' => (float) $customer->balance_due,
            ])
            ->filter(fn (array $row) => $row['balance'] > 0.005)
            ->sortByDesc('balance')
            ->values()
            ->all();

        $customerPaymentsList = $payments->groupBy('customer_id')->map(function ($custPayments) {
            $first = $custPayments->first();
            $customer = $first->customer;
            $totalPaid = (float) $custPayments->sum('amount_paid');
            $methods = $custPayments->map(fn (Payment $p) => $p->payment_method?->label() ?? '—')->filter()->unique()->join(', ');
            $latestDate = $custPayments->max(fn (Payment $p) => $p->paid_at ?? $p->created_at);

            return [
                'customer' => $customer?->name ?? '—',
                'payments_count' => $custPayments->count(),
                'methods' => $methods ?: '—',
                'latest_date' => $latestDate ? Carbon::parse($latestDate)->format('Y-m-d') : '—',
                'total_paid' => $totalPaid,
            ];
        })->sortByDesc('total_paid')->values();

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
                'earnings' => $earnings,
                'collected' => $collected,
                'balance' => $balance,
                'newCustomers' => Customer::whereBetween('created_at', [$start, $end])->count(),
                'requests' => $requests->count(),
                'packages' => Package::whereBetween('created_at', [$start, $end])->count(),
                'shipments' => Shipment::whereBetween('created_at', [$start, $end])->count(),
                'payments' => $payments->count(),
            ],
            'summary' => [
                __('Total Facturado') => $invoiced,
                __('Ganancia Servicios') => $earnings,
                __('Total Pagado por Clientes') => $collected,
                __('Saldo por Cobrar') => $balance,
                __('New Customers') => Customer::whereBetween('created_at', [$start, $end])->count(),
                __('Requests') => $requests->count(),
                __('Packages') => Package::whereBetween('created_at', [$start, $end])->count(),
                __('Shipments') => Shipment::whereBetween('created_at', [$start, $end])->count(),
            ],
            'invoices' => $invoicesList->all(),
            'customerPayments' => $customerPaymentsList->all(),
            'payments' => $allRecords,
            'balanceByCustomer' => $balanceByCustomer,
            'revenue' => $this->revenueBuckets($payments),
        ];
    }

    /** @return array<int, array{label: string, total: float}> */
    protected function revenueBuckets($payments): array
    {
        $groupBy = $this->period === 'yearly' ? 'Y-m' : 'Y-m-d';
        $buckets = $payments->groupBy(function (Payment $p) use ($groupBy) {
            $date = $p->paid_at ?? $p->created_at;

            return $date ? $date->format($groupBy) : now()->format($groupBy);
        });

        $rows = [];
        foreach ($buckets as $key => $group) {
            $rows[] = [
                'label' => $this->period === 'yearly'
                    ? Carbon::createFromFormat('Y-m', $key)->translatedFormat('M Y')
                    : Carbon::createFromFormat('Y-m-d', $key)->translatedFormat('M d'),
                'total' => (float) $group->sum('amount_paid'),
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
            $p->payment_method?->label() ?? '',
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
