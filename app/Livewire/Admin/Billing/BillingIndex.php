<?php

namespace App\Livewire\Admin\Billing;

use App\Concerns\SwalNotifies;
use App\Enums\CostType;
use App\Enums\PaymentMethod;
use App\Enums\RequestStatus;
use App\Models\CostItem;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Facturación')]
class BillingIndex extends Component
{
    use SwalNotifies, WithPagination;

    public string $search = '';

    public string $paymentFilter = 'all';

    public bool $showCreateModal = false;

    public bool $showPaymentModal = false;

    public ?int $selectedRequestId = null;

    public array $invoiceForm = [
        'customer_id' => null,
        'customer_search' => '',
        'notes' => '',
        'items' => [],
        'costs' => [],
        'amount_paid' => 0.0,
        'payment_method' => 'zelle',
        'payment_reference' => '',
        'paid_at' => null,
    ];

    public array $paymentForm = [
        'request_id' => null,
        'customer_id' => null,
        'invoice_total' => 0.0,
        'amount_paid' => 0.0,
        'payment_method' => 'zelle',
        'reference' => '',
        'paid_at' => null,
        'notes' => '',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPaymentFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->invoiceForm = [
            'customer_id' => null,
            'customer_search' => '',
            'notes' => '',
            'items' => [
                [
                    'product_name' => '',
                    'store' => '',
                    'quantity' => 1,
                    'unit_price' => 0.0,
                ],
            ],
            'costs' => [
                [
                    'type' => 'personal_shopper',
                    'description' => 'Comisión Personal Shopper',
                    'amount' => 0.0,
                ],
            ],
            'amount_paid' => 0.0,
            'payment_method' => 'zelle',
            'payment_reference' => '',
            'paid_at' => now()->toDateString(),
        ];
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function selectCustomer(?int $customerId, string $name): void
    {
        $this->invoiceForm['customer_id'] = $customerId;
        $this->invoiceForm['customer_search'] = $name;
    }

    public function addItem(): void
    {
        $this->invoiceForm['items'][] = [
            'product_name' => '',
            'store' => '',
            'quantity' => 1,
            'unit_price' => 0.0,
        ];
    }

    public function removeItem(int $index): void
    {
        unset($this->invoiceForm['items'][$index]);
        $this->invoiceForm['items'] = array_values($this->invoiceForm['items']);
    }

    public function addCost(): void
    {
        $this->invoiceForm['costs'][] = [
            'type' => 'other',
            'description' => '',
            'amount' => 0.0,
        ];
    }

    public function removeCost(int $index): void
    {
        unset($this->invoiceForm['costs'][$index]);
        $this->invoiceForm['costs'] = array_values($this->invoiceForm['costs']);
    }

    public function getInvoicedTotalProperty(): float
    {
        $itemsTotal = 0.0;
        foreach ($this->invoiceForm['items'] as $item) {
            $qty = (int) ($item['quantity'] ?? 1);
            $price = (float) ($item['unit_price'] ?? 0);
            $itemsTotal += ($qty * $price);
        }

        $costsTotal = 0.0;
        foreach ($this->invoiceForm['costs'] as $cost) {
            $costsTotal += (float) ($cost['amount'] ?? 0);
        }

        return (float) ($itemsTotal + $costsTotal);
    }

    public function getPendingBalanceProperty(): float
    {
        $total = $this->invoicedTotal;
        $paid = (float) ($this->invoiceForm['amount_paid'] ?? 0);

        return max(0.0, (float) ($total - $paid));
    }

    public function payFullAmount(): void
    {
        $this->invoiceForm['amount_paid'] = $this->invoicedTotal;
    }

    public function saveInvoice(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $this->validate([
            'invoiceForm.customer_id' => ['required', 'exists:customers,id'],
            'invoiceForm.items' => ['required', 'array', 'min:1'],
            'invoiceForm.items.*.product_name' => ['required', 'string', 'max:255'],
            'invoiceForm.items.*.quantity' => ['required', 'integer', 'min:1'],
            'invoiceForm.items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'invoiceForm.costs.*.amount' => ['nullable', 'numeric', 'min:0'],
            'invoiceForm.amount_paid' => ['nullable', 'numeric', 'min:0'],
            'invoiceForm.payment_method' => ['required', 'string'],
        ], [
            'invoiceForm.customer_id.required' => __('Please select a customer for this invoice.'),
            'invoiceForm.items.min' => __('Please add at least one product line.'),
            'invoiceForm.items.*.product_name.required' => __('Product name is required.'),
        ]);

        $firstItem = $this->invoiceForm['items'][0];
        $allProductsSummary = collect($this->invoiceForm['items'])->pluck('product_name')->join(', ');

        $purchaseRequest = PurchaseRequest::create([
            'customer_id' => $this->invoiceForm['customer_id'],
            'product_name' => count($this->invoiceForm['items']) > 1 ? $allProductsSummary : $firstItem['product_name'],
            'store' => $firstItem['store'] ?? null,
            'quantity' => $firstItem['quantity'] ?? 1,
            'unit_price' => $firstItem['unit_price'] ?? null,
            'status' => RequestStatus::Quoted,
            'notes' => $this->invoiceForm['notes'] ?? null,
        ]);

        // Add product cost items
        foreach ($this->invoiceForm['items'] as $item) {
            $qty = (int) ($item['quantity'] ?? 1);
            $price = (float) ($item['unit_price'] ?? 0);
            $subtotal = $qty * $price;

            if ($subtotal > 0) {
                CostItem::create([
                    'costable_type' => PurchaseRequest::class,
                    'costable_id' => $purchaseRequest->id,
                    'type' => CostType::ProductCost,
                    'description' => $item['product_name'].($qty > 1 ? " ({$qty} x $".number_format($price, 2).')' : ''),
                    'amount' => $subtotal,
                ]);
            }
        }

        // Add additional cost items
        foreach ($this->invoiceForm['costs'] as $cost) {
            $amount = (float) ($cost['amount'] ?? 0);
            if ($amount > 0) {
                $costType = CostType::tryFrom($cost['type'] ?? 'other') ?? CostType::Other;
                CostItem::create([
                    'costable_type' => PurchaseRequest::class,
                    'costable_id' => $purchaseRequest->id,
                    'type' => $costType,
                    'description' => $cost['description'] ?? $costType->label(),
                    'amount' => $amount,
                ]);
            }
        }

        $totalInvoiced = $this->invoicedTotal;
        $amountPaid = (float) ($this->invoiceForm['amount_paid'] ?? 0);

        // Record initial payment if amount_paid > 0
        if ($amountPaid > 0) {
            Payment::create([
                'customer_id' => $this->invoiceForm['customer_id'],
                'billable_type' => PurchaseRequest::class,
                'billable_id' => $purchaseRequest->id,
                'invoice_total' => $totalInvoiced,
                'amount_paid' => $amountPaid,
                'payment_method' => PaymentMethod::tryFrom($this->invoiceForm['payment_method']) ?? PaymentMethod::Zelle,
                'reference' => $this->invoiceForm['payment_reference'] ?? null,
                'paid_at' => $this->invoiceForm['paid_at'] ? now()->parse($this->invoiceForm['paid_at']) : now(),
                'notes' => 'Pago registrado al emitir factura '.$purchaseRequest->number,
            ]);
        }

        $this->showCreateModal = false;
        $this->swalSuccess(__('Factura :number creada correctamente con sus productos y pago.', ['number' => $purchaseRequest->number]));
    }

    public function openPaymentModal(int $requestId): void
    {
        $this->resetValidation();
        $request = PurchaseRequest::with('customer', 'costItems')->findOrFail($requestId);
        $totalCost = (float) $request->total_cost;
        $alreadyPaid = (float) Payment::where('billable_type', PurchaseRequest::class)
            ->where('billable_id', $request->id)
            ->sum('amount_paid');

        $pending = max(0.0, $totalCost - $alreadyPaid);

        $this->paymentForm = [
            'request_id' => $request->id,
            'customer_id' => $request->customer_id,
            'customer_name' => $request->customer?->name,
            'request_number' => $request->number,
            'invoice_total' => $totalCost,
            'already_paid' => $alreadyPaid,
            'pending_balance' => $pending,
            'amount_paid' => $pending,
            'payment_method' => 'zelle',
            'reference' => '',
            'paid_at' => now()->toDateString(),
            'notes' => '',
        ];

        $this->showPaymentModal = true;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->resetValidation();
    }

    public function savePayment(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $this->validate([
            'paymentForm.amount_paid' => ['required', 'numeric', 'min:0.01'],
            'paymentForm.payment_method' => ['required', 'string'],
        ]);

        Payment::create([
            'customer_id' => $this->paymentForm['customer_id'],
            'billable_type' => PurchaseRequest::class,
            'billable_id' => $this->paymentForm['request_id'],
            'invoice_total' => $this->paymentForm['invoice_total'],
            'amount_paid' => (float) $this->paymentForm['amount_paid'],
            'payment_method' => PaymentMethod::tryFrom($this->paymentForm['payment_method']) ?? PaymentMethod::Zelle,
            'reference' => $this->paymentForm['reference'] ?? null,
            'paid_at' => $this->paymentForm['paid_at'] ? now()->parse($this->paymentForm['paid_at']) : now(),
            'notes' => $this->paymentForm['notes'] ?? 'Abono registrado a factura '.$this->paymentForm['request_number'],
        ]);

        $this->showPaymentModal = false;
        $this->swalSuccess(__('Abono de :amount registrado correctamente.', ['amount' => money($this->paymentForm['amount_paid'])]));
    }

    public function render()
    {
        $query = PurchaseRequest::query()
            ->with(['customer', 'costItems'])
            ->latest();

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('number', 'like', "%{$this->search}%")
                    ->orWhere('product_name', 'like', "%{$this->search}%")
                    ->orWhereHas('customer', function ($c) {
                        $c->where('name', 'like', "%{$this->search}%")
                            ->orWhere('phone', 'like', "%{$this->search}%")
                            ->orWhere('email', 'like', "%{$this->search}%");
                    });
            });
        }

        $requests = $query->paginate(15);

        // Preload payments to calculate paid and balance per request
        $requestIds = $requests->pluck('id')->toArray();
        $paymentsByRequest = Payment::where('billable_type', PurchaseRequest::class)
            ->whereIn('billable_id', $requestIds)
            ->get()
            ->groupBy('billable_id');

        $totalInvoiced = CostItem::where('costable_type', PurchaseRequest::class)->sum('amount');
        $totalCollected = Payment::sum('amount_paid');
        $totalPending = max(0.0, $totalInvoiced - $totalCollected);

        return view('livewire.admin.billing.billing-index', [
            'requests' => $requests,
            'paymentsByRequest' => $paymentsByRequest,
            'totalInvoiced' => $totalInvoiced,
            'totalCollected' => $totalCollected,
            'totalPending' => $totalPending,
            'customers' => Customer::orderBy('name')->get(['id', 'name', 'phone', 'email', 'country']),
        ]);
    }
}
