<?php

namespace App\Livewire\Admin\Billing;

use App\Concerns\SwalNotifies;
use App\Enums\CostType;
use App\Enums\PaymentMethod;
use App\Enums\RequestStatus;
use App\Mail\InvoiceCreatedMail;
use App\Models\CostItem;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Setting;
use App\Services\InvoicePdfService;
use App\Services\PricingRateService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

    public bool $showCreateForm = false;

    public bool $isEditing = false;

    public ?int $editingRequestId = null;

    public ?string $editingRequestNumber = null;

    public bool $showPaymentModal = false;

    public ?int $selectedRequestId = null;

    public array $rates = [];

    public string $serviceType = 'shopper'; // 'shopper', 'online', 'repack'

    public string $invoiceType = 'cotizacion'; // 'cotizacion', 'pendiente', 'pagado'

    public array $guidedQuestions = [
        'apply_shopper_commission' => true,
        'extra_stores_count' => 0,
        'boxes_small_count' => 0,
        'boxes_medium_count' => 0,
        'boxes_large_count' => 0,
        'apply_warehouse_commission' => false,
        'warehouse_delivery_count' => 0,
        'storage_months_count' => 0,
    ];

    public array $customCosts = [];

    public array $invoiceForm = [
        'customer_id' => null,
        'customer_search' => '',
        'invoice_type' => 'cotizacion',
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

    // Keep $showCreateModal as alias for backward compatibility with tests
    public function getShowCreateModalProperty(): bool
    {
        return $this->showCreateForm;
    }

    public function setShowCreateModalProperty(bool $value): void
    {
        $this->showCreateForm = $value;
    }

    public function mount(?PricingRateService $rateService = null): void
    {
        $rateService ??= app(PricingRateService::class);
        $this->rates = $rateService->getRates();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPaymentFilter(): void
    {
        $this->resetPage();
    }

    public function toggleCreateForm(): void
    {
        if ($this->showCreateForm) {
            $this->closeCreateForm();
        } else {
            $this->openCreateForm();
        }
    }

    public function openCreateForm(): void
    {
        $this->resetValidation();
        $this->rates = app(PricingRateService::class)->getRates();

        $this->isEditing = false;
        $this->editingRequestId = null;
        $this->editingRequestNumber = null;
        $this->serviceType = 'shopper';

        $this->guidedQuestions = [
            'apply_shopper_commission' => true,
            'extra_stores_count' => 0,
            'boxes_small_count' => 0,
            'boxes_medium_count' => 0,
            'boxes_large_count' => 0,
            'apply_warehouse_commission' => false,
            'warehouse_delivery_count' => 0,
            'storage_months_count' => 0,
        ];
        $this->customCosts = [];

        $this->invoiceType = 'cotizacion';
        $this->invoiceForm = [
            'customer_id' => null,
            'customer_search' => '',
            'invoice_type' => 'cotizacion',
            'notes' => '',
            'items' => [
                [
                    'product_name' => '',
                    'store' => '',
                    'quantity' => 1,
                    'unit_price' => 0.0,
                ],
            ],
            'costs' => [],
            'amount_paid' => 0.0,
            'payment_method' => 'zelle',
            'payment_reference' => '',
            'paid_at' => now()->toDateString(),
        ];

        $this->syncGuidedQuestionsToCosts();
        $this->showCreateForm = true;
    }

    public function setInvoiceType(string $type): void
    {
        $this->invoiceType = in_array($type, ['cotizacion', 'pendiente', 'pagado'], true) ? $type : 'cotizacion';
        $this->invoiceForm['invoice_type'] = $this->invoiceType;

        if ($this->invoiceType === 'pagado') {
            $this->invoiceForm['amount_paid'] = $this->invoicedTotal;
        } elseif ($this->invoiceType === 'cotizacion') {
            $this->invoiceForm['amount_paid'] = 0.0;
        }
    }

    public function setServiceType(string $type): void
    {
        $this->serviceType = in_array($type, ['shopper', 'online', 'repack'], true) ? $type : 'shopper';

        if ($this->serviceType === 'online') {
            $this->guidedQuestions['apply_shopper_commission'] = false;
            $this->guidedQuestions['apply_warehouse_commission'] = true;
            $this->guidedQuestions['warehouse_delivery_count'] = max(1, (int) ($this->guidedQuestions['warehouse_delivery_count'] ?? 0));
        } elseif ($this->serviceType === 'repack') {
            $this->guidedQuestions['apply_shopper_commission'] = false;
            $this->guidedQuestions['apply_warehouse_commission'] = false;
            $this->guidedQuestions['warehouse_delivery_count'] = max(1, (int) ($this->guidedQuestions['warehouse_delivery_count'] ?? 0));
        } else {
            $this->guidedQuestions['apply_shopper_commission'] = true;
            $this->guidedQuestions['apply_warehouse_commission'] = false;
        }

        $this->syncGuidedQuestionsToCosts();
    }

    public function editInvoice(int $requestId): void
    {
        $this->resetValidation();
        $this->rates = app(PricingRateService::class)->getRates();

        $request = PurchaseRequest::with(['customer', 'costItems'])->findOrFail($requestId);

        $this->isEditing = true;
        $this->editingRequestId = $request->id;
        $this->editingRequestNumber = $request->number;

        // Detect service type based on cost items
        $hasReceivingCommission = $request->costItems->where('type', CostType::ReceivingFee)->filter(fn ($c) => stripos($c->description, 'Comisión') !== false)->isNotEmpty();
        $hasShopperFee = $request->costItems->where('type', CostType::ShopperFee)->isNotEmpty();
        $hasBoxesOrDelivery = $request->costItems->whereIn('type', [CostType::PackingFee, CostType::ReceivingFee])->isNotEmpty();
        $productCosts = $request->costItems->where('type', CostType::ProductCost);
        $productTotal = (float) $productCosts->sum('amount');

        if ($hasReceivingCommission) {
            $this->serviceType = 'online';
        } elseif ($hasBoxesOrDelivery && ($productTotal === 0.0 || ! $hasShopperFee)) {
            $this->serviceType = 'repack';
        } else {
            $this->serviceType = 'shopper';
        }

        // Populate items
        $items = [];
        if ($productCosts->isNotEmpty()) {
            foreach ($productCosts as $cost) {
                // Extract price if format contains "(Comprado online - Pagado en internet: $XX.XX)"
                $unitPrice = (float) $cost->amount;
                if (($this->serviceType === 'online' || $this->serviceType === 'repack') && preg_match('/Pagado en internet:\s*\$([0-9\.,]+)/i', $cost->description ?? '', $m)) {
                    $unitPrice = (float) str_replace(',', '', $m[1]);
                }

                $cleanName = preg_replace('/\s*\(.*?\)\s*/', '', (string) ($cost->description ?: $request->product_name));

                $items[] = [
                    'product_name' => $cleanName ?: ($request->product_name ?: 'Producto'),
                    'store' => $request->store ?: '',
                    'quantity' => 1,
                    'unit_price' => $unitPrice,
                ];
            }
        } else {
            $items[] = [
                'product_name' => $request->product_name ?: '',
                'store' => $request->store ?: '',
                'quantity' => $request->quantity ?: 1,
                'unit_price' => (float) ($request->unit_price ?: 0.0),
            ];
        }

        // Initialize guided questions based on existing non-product cost items
        $this->guidedQuestions = [
            'apply_shopper_commission' => $this->serviceType === 'shopper',
            'extra_stores_count' => 0,
            'boxes_small_count' => 0,
            'boxes_medium_count' => 0,
            'boxes_large_count' => 0,
            'apply_warehouse_commission' => $this->serviceType === 'online',
            'warehouse_delivery_count' => ($this->serviceType === 'online' || $this->serviceType === 'repack') ? 1 : 0,
            'storage_months_count' => 0,
        ];
        $this->customCosts = [];

        $nonProductCosts = $request->costItems->where('type', '!=', CostType::ProductCost);

        foreach ($nonProductCosts as $cost) {
            $desc = $cost->description ?? '';
            $amount = (float) $cost->amount;

            if (stripos($desc, 'Personal Shopper') !== false || $cost->type === CostType::ShopperFee && stripos($desc, 'Comisión') !== false) {
                $this->guidedQuestions['apply_shopper_commission'] = true;
            } elseif (stripos($desc, 'Tienda Adicional') !== false || stripos($desc, 'Additional Store') !== false) {
                $unitFee = (float) ($this->rates['extra_store_fee'] ?? 20.0);
                $qty = $unitFee > 0 ? (int) round($amount / $unitFee) : 1;
                $this->guidedQuestions['extra_stores_count'] += max(1, $qty);
            } elseif (stripos($desc, 'Small') !== false) {
                $unitFee = (float) ($this->rates['box_small_heavy_duty'] ?? 15.0);
                $qty = $unitFee > 0 ? (int) round($amount / $unitFee) : 1;
                $this->guidedQuestions['boxes_small_count'] += max(1, $qty);
            } elseif (stripos($desc, 'Mediana') !== false || stripos($desc, 'Medium') !== false) {
                $unitFee = (float) ($this->rates['box_medium_heavy_duty'] ?? 20.0);
                $qty = $unitFee > 0 ? (int) round($amount / $unitFee) : 1;
                $this->guidedQuestions['boxes_medium_count'] += max(1, $qty);
            } elseif (stripos($desc, 'Larga') !== false || stripos($desc, 'Large') !== false) {
                $unitFee = (float) ($this->rates['box_large_heavy_duty'] ?? 25.0);
                $qty = $unitFee > 0 ? (int) round($amount / $unitFee) : 1;
                $this->guidedQuestions['boxes_large_count'] += max(1, $qty);
            } elseif (stripos($desc, 'Comisión Almacén') !== false || stripos($desc, 'Warehouse Commission') !== false) {
                $this->guidedQuestions['apply_warehouse_commission'] = true;
            } elseif (stripos($desc, 'Llevar') !== false || stripos($desc, 'Traslado') !== false || stripos($desc, 'Drop-off') !== false) {
                $unitFee = (float) ($this->rates['warehouse_delivery_fee'] ?? 20.0);
                $qty = $unitFee > 0 ? (int) round($amount / $unitFee) : 1;
                $this->guidedQuestions['warehouse_delivery_count'] = max(1, $qty);
            } elseif (stripos($desc, 'Almacenaje') !== false || stripos($desc, 'Storage') !== false) {
                $unitFee = (float) ($this->rates['monthly_storage_fee'] ?? 15.0);
                $qty = $unitFee > 0 ? (int) round($amount / $unitFee) : 1;
                $this->guidedQuestions['storage_months_count'] += max(1, $qty);
            } else {
                $this->customCosts[] = [
                    'type' => $cost->type->value ?? 'other',
                    'description' => $desc,
                    'amount' => $amount,
                ];
            }
        }

        // Get already paid amount for this request
        $alreadyPaid = (float) Payment::where('billable_type', PurchaseRequest::class)
            ->where('billable_id', $request->id)
            ->sum('amount_paid');

        if ($request->status === RequestStatus::Quoted) {
            $this->invoiceType = 'cotizacion';
        } elseif ($request->status === RequestStatus::AwaitingPayment) {
            $this->invoiceType = 'pendiente';
        } else {
            $this->invoiceType = 'pagado';
        }

        $this->invoiceForm = [
            'customer_id' => $request->customer_id,
            'customer_search' => $request->customer?->name ?? '',
            'invoice_type' => $this->invoiceType,
            'notes' => $request->notes ?? '',
            'items' => $items,
            'costs' => [],
            'amount_paid' => $alreadyPaid,
            'payment_method' => 'zelle',
            'payment_reference' => '',
            'paid_at' => now()->toDateString(),
        ];

        $this->syncGuidedQuestionsToCosts();
        $this->showCreateForm = true;
    }

    public function closeCreateForm(): void
    {
        $this->showCreateForm = false;
        $this->isEditing = false;
        $this->editingRequestId = null;
        $this->editingRequestNumber = null;
        $this->resetValidation();
    }

    public function openCreateModal(): void
    {
        $this->openCreateForm();
    }

    public function closeCreateModal(): void
    {
        $this->closeCreateForm();
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
        $this->syncGuidedQuestionsToCosts();
    }

    public function addCustomCost(): void
    {
        $this->customCosts[] = [
            'type' => 'other',
            'description' => '',
            'amount' => 0.0,
        ];
        $this->syncGuidedQuestionsToCosts();
    }

    public function removeCustomCost(int $index): void
    {
        unset($this->customCosts[$index]);
        $this->customCosts = array_values($this->customCosts);
        $this->syncGuidedQuestionsToCosts();
    }

    public function toggleQuestion(string $key): void
    {
        if (isset($this->guidedQuestions[$key])) {
            $this->guidedQuestions[$key] = ! $this->guidedQuestions[$key];
            $this->syncGuidedQuestionsToCosts();
        }
    }

    public function incrementQuestion(string $key): void
    {
        if (isset($this->guidedQuestions[$key])) {
            $this->guidedQuestions[$key] = ((int) $this->guidedQuestions[$key]) + 1;
            $this->syncGuidedQuestionsToCosts();
        }
    }

    public function decrementQuestion(string $key): void
    {
        if (isset($this->guidedQuestions[$key])) {
            $this->guidedQuestions[$key] = max(0, ((int) $this->guidedQuestions[$key]) - 1);
            $this->syncGuidedQuestionsToCosts();
        }
    }

    public function getProductsSubtotalProperty(): float
    {
        $subtotal = 0.0;
        foreach ($this->invoiceForm['items'] as $item) {
            $qty = (int) ($item['quantity'] ?? 1);
            $price = (float) ($item['unit_price'] ?? 0);
            $subtotal += ($qty * $price);
        }

        return (float) $subtotal;
    }

    public function getShopperCommissionCalculationProperty(): array
    {
        $subtotal = $this->productsSubtotal;
        $tiers = $this->rates['shopper_tiers'] ?? [];

        $matchedTier = null;
        foreach ($tiers as $tier) {
            $min = (float) ($tier['min'] ?? 0);
            $max = $tier['max'] !== null && $tier['max'] !== '' ? (float) $tier['max'] : null;

            if ($max !== null) {
                if ($subtotal >= $min && $subtotal <= $max) {
                    $matchedTier = $tier;
                    break;
                }
            } else {
                if ($subtotal >= $min) {
                    $matchedTier = $tier;
                    break;
                }
            }
        }

        if (! $matchedTier && ! empty($tiers)) {
            $matchedTier = $tiers[0];
        }

        $percent = (float) ($matchedTier['percent'] ?? 20.0);
        $stores = (int) ($matchedTier['stores'] ?? 2);
        $hours = (int) ($matchedTier['hours'] ?? 2);
        $amount = round($subtotal * ($percent / 100), 2);

        return [
            'percent' => $percent,
            'stores' => $stores,
            'hours' => $hours,
            'amount' => $amount,
            'tier' => $matchedTier,
        ];
    }

    public function syncGuidedQuestionsToCosts(): void
    {
        $costs = [];
        $subtotal = $this->productsSubtotal;
        $calc = $this->shopperCommissionCalculation;

        // 1. Comisión Personal Shopper (solo compras físicas)
        if ($this->serviceType === 'shopper' && ! empty($this->guidedQuestions['apply_shopper_commission'])) {
            $costs[] = [
                'preset' => 'shopper_commission',
                'type' => 'shopper_fee',
                'description' => "Comisión Personal Shopper ({$calc['percent']}% - {$calc['stores']} tiendas / {$calc['hours']} hrs)",
                'amount' => $calc['amount'],
            ];
        }

        // 2. Tiendas adicionales (solo compras físicas)
        $extraStores = (int) ($this->guidedQuestions['extra_stores_count'] ?? 0);
        if ($this->serviceType === 'shopper' && $extraStores > 0) {
            $rate = (float) ($this->rates['extra_store_fee'] ?? 20.0);
            $costs[] = [
                'preset' => 'extra_store',
                'type' => 'shopper_fee',
                'description' => "Visita a Tienda Adicional ({$extraStores} x $".number_format($rate, 2).')',
                'amount' => round($extraStores * $rate, 2),
            ];
        }

        // 3. Cajas Heavy Duty (aplica a cualquier servicio de reempaque)
        $boxSmall = (int) ($this->guidedQuestions['boxes_small_count'] ?? 0);
        if ($boxSmall > 0) {
            $rate = (float) ($this->rates['box_small_heavy_duty'] ?? 15.0);
            $costs[] = [
                'preset' => 'box_small',
                'type' => 'packing_fee',
                'description' => "1 Caja Small Heavy Duty ({$boxSmall} x $".number_format($rate, 2).')',
                'amount' => round($boxSmall * $rate, 2),
            ];
        }

        $boxMed = (int) ($this->guidedQuestions['boxes_medium_count'] ?? 0);
        if ($boxMed > 0) {
            $rate = (float) ($this->rates['box_medium_heavy_duty'] ?? 20.0);
            $costs[] = [
                'preset' => 'box_medium',
                'type' => 'packing_fee',
                'description' => "1 Caja Mediana Heavy Duty ({$boxMed} x $".number_format($rate, 2).')',
                'amount' => round($boxMed * $rate, 2),
            ];
        }

        $boxLarge = (int) ($this->guidedQuestions['boxes_large_count'] ?? 0);
        if ($boxLarge > 0) {
            $rate = (float) ($this->rates['box_large_heavy_duty'] ?? 25.0);
            $costs[] = [
                'preset' => 'box_large',
                'type' => 'packing_fee',
                'description' => "1 Caja Larga Heavy Duty ({$boxLarge} x $".number_format($rate, 2).')',
                'amount' => round($boxLarge * $rate, 2),
            ];
        }

        // 4. Comisión Almacén / Compras Online (15% sobre el valor del pedido online - no aplica en reempaque)
        if ($this->serviceType === 'online' || ($this->serviceType !== 'repack' && ! empty($this->guidedQuestions['apply_warehouse_commission']))) {
            $pct = (float) ($this->rates['warehouse_percent'] ?? 15.0);
            $costs[] = [
                'preset' => 'warehouse_commission',
                'type' => 'receiving_fee',
                'description' => "Comisión Almacén / Compras Online ({$pct}% sobre $".number_format($subtotal, 2).')',
                'amount' => round($subtotal * ($pct / 100), 2),
            ];
        }

        // 5. Traslado de caja al Almacén (Fijo $20 en compras online y reempaque)
        $delivery = (int) ($this->guidedQuestions['warehouse_delivery_count'] ?? 0);
        if (($this->serviceType === 'online' || $this->serviceType === 'repack') && $delivery === 0) {
            $delivery = 1;
            $this->guidedQuestions['warehouse_delivery_count'] = 1;
        }

        if ($delivery > 0) {
            $rate = (float) ($this->rates['warehouse_delivery_fee'] ?? 20.0);
            $costs[] = [
                'preset' => 'warehouse_delivery',
                'type' => 'receiving_fee',
                'description' => 'Servicio de Traslado de Caja al Almacén (Fijo $'.number_format($rate, 2).($delivery > 1 ? " x {$delivery}" : '').')',
                'amount' => round($delivery * $rate, 2),
            ];
        }

        // 6. Almacenaje tras 30 días
        $storageMonths = (int) ($this->guidedQuestions['storage_months_count'] ?? 0);
        if ($storageMonths > 0) {
            $rate = (float) ($this->rates['monthly_storage_fee'] ?? 15.0);
            $costs[] = [
                'preset' => 'monthly_storage',
                'type' => 'other',
                'description' => "Almacenaje en Almacén ({$storageMonths} mes(es) tras 30 días)",
                'amount' => round($storageMonths * $rate, 2),
            ];
        }

        // Custom additional costs
        foreach ($this->customCosts as $custom) {
            if (! empty($custom['description']) || (float) ($custom['amount'] ?? 0) > 0) {
                $costs[] = $custom;
            }
        }

        $this->invoiceForm['costs'] = $costs;
    }

    public function syncShopperCommissionRates(): void
    {
        $this->syncGuidedQuestionsToCosts();
    }

    public function addQuickRateCost(string $presetKey): void
    {
        switch ($presetKey) {
            case 'shopper_commission':
                $this->guidedQuestions['apply_shopper_commission'] = true;
                break;
            case 'box_small':
                $this->guidedQuestions['boxes_small_count'] = ((int) $this->guidedQuestions['boxes_small_count']) + 1;
                break;
            case 'box_medium':
                $this->guidedQuestions['boxes_medium_count'] = ((int) $this->guidedQuestions['boxes_medium_count']) + 1;
                break;
            case 'box_large':
                $this->guidedQuestions['boxes_large_count'] = ((int) $this->guidedQuestions['boxes_large_count']) + 1;
                break;
            case 'extra_store':
                $this->guidedQuestions['extra_stores_count'] = ((int) $this->guidedQuestions['extra_stores_count']) + 1;
                break;
            case 'warehouse_delivery':
                $this->guidedQuestions['warehouse_delivery_count'] = ((int) $this->guidedQuestions['warehouse_delivery_count']) + 1;
                break;
            case 'warehouse_commission':
                $this->guidedQuestions['apply_warehouse_commission'] = true;
                break;
            case 'monthly_storage':
                $this->guidedQuestions['storage_months_count'] = ((int) $this->guidedQuestions['storage_months_count']) + 1;
                break;
        }

        $this->syncGuidedQuestionsToCosts();
    }

    public function updatedGuidedQuestions(): void
    {
        $this->syncGuidedQuestionsToCosts();
    }

    public function updatedCustomCosts(): void
    {
        $this->syncGuidedQuestionsToCosts();
    }

    public function updatedInvoiceFormItems(): void
    {
        $this->syncGuidedQuestionsToCosts();
    }

    public function getInvoicedTotalProperty(): float
    {
        // En compras online, lo que el cliente pagó en internet no se suma al total a cobrar en la factura
        $itemsTotal = $this->serviceType === 'shopper' ? $this->productsSubtotal : 0.0;
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

        $this->syncGuidedQuestionsToCosts();

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

        $type = $this->invoiceForm['invoice_type'] ?? $this->invoiceType;
        $targetStatus = match ($type) {
            'cotizacion' => RequestStatus::Quoted,
            'pendiente' => RequestStatus::AwaitingPayment,
            'pagado' => RequestStatus::Purchased,
            default => RequestStatus::Quoted,
        };

        if ($this->isEditing && $this->editingRequestId) {
            $purchaseRequest = PurchaseRequest::findOrFail($this->editingRequestId);

            $purchaseRequest->update([
                'customer_id' => $this->invoiceForm['customer_id'],
                'product_name' => count($this->invoiceForm['items']) > 1 ? $allProductsSummary : $firstItem['product_name'],
                'store' => $firstItem['store'] ?? null,
                'quantity' => $firstItem['quantity'] ?? 1,
                'unit_price' => $this->serviceType === 'shopper' ? ($firstItem['unit_price'] ?? null) : 0.0,
                'status' => $targetStatus,
                'notes' => $this->invoiceForm['notes'] ?? null,
            ]);

            // Delete existing cost items and rebuild them
            CostItem::where('costable_type', PurchaseRequest::class)
                ->where('costable_id', $purchaseRequest->id)
                ->delete();

            // Re-add product cost items
            foreach ($this->invoiceForm['items'] as $item) {
                $qty = (int) ($item['quantity'] ?? 1);
                $price = (float) ($item['unit_price'] ?? 0);
                $subtotal = $qty * $price;

                if ($this->serviceType === 'shopper') {
                    if ($subtotal > 0) {
                        CostItem::create([
                            'costable_type' => PurchaseRequest::class,
                            'costable_id' => $purchaseRequest->id,
                            'type' => CostType::ProductCost,
                            'description' => $item['product_name'].($qty > 1 ? " ({$qty} x $".number_format($price, 2).')' : ''),
                            'amount' => $subtotal,
                        ]);
                    }
                } else {
                    // Online purchase: $0 billable amount
                    CostItem::create([
                        'costable_type' => PurchaseRequest::class,
                        'costable_id' => $purchaseRequest->id,
                        'type' => CostType::ProductCost,
                        'description' => $item['product_name'].' (Comprado online - Pagado en internet: $'.number_format($subtotal, 2).')',
                        'amount' => 0.0,
                    ]);
                }
            }

            // Re-add additional cost items
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

            $this->showCreateForm = false;
            $this->isEditing = false;
            $this->editingRequestId = null;
            $this->editingRequestNumber = null;
            $this->swalSuccess(__('Factura :number actualizada correctamente.', ['number' => $purchaseRequest->number]));

            return;
        }

        $purchaseRequest = PurchaseRequest::create([
            'customer_id' => $this->invoiceForm['customer_id'],
            'product_name' => count($this->invoiceForm['items']) > 1 ? $allProductsSummary : $firstItem['product_name'],
            'store' => $firstItem['store'] ?? null,
            'quantity' => $firstItem['quantity'] ?? 1,
            'unit_price' => $this->serviceType === 'shopper' ? ($firstItem['unit_price'] ?? null) : 0.0,
            'status' => $targetStatus,
            'notes' => $this->invoiceForm['notes'] ?? null,
        ]);

        // Add product cost items
        foreach ($this->invoiceForm['items'] as $item) {
            $qty = (int) ($item['quantity'] ?? 1);
            $price = (float) ($item['unit_price'] ?? 0);
            $subtotal = $qty * $price;

            if ($this->serviceType === 'shopper') {
                if ($subtotal > 0) {
                    CostItem::create([
                        'costable_type' => PurchaseRequest::class,
                        'costable_id' => $purchaseRequest->id,
                        'type' => CostType::ProductCost,
                        'description' => $item['product_name'].($qty > 1 ? " ({$qty} x $".number_format($price, 2).')' : ''),
                        'amount' => $subtotal,
                    ]);
                }
            } else {
                // Online purchase: $0 billable amount
                CostItem::create([
                    'costable_type' => PurchaseRequest::class,
                    'costable_id' => $purchaseRequest->id,
                    'type' => CostType::ProductCost,
                    'description' => $item['product_name'].' (Comprado online - Pagado en internet: $'.number_format($subtotal, 2).')',
                    'amount' => 0.0,
                ]);
            }
        }

        // Add additional cost items (Personal shopper, boxes, fees from rate sheet)
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

        // Automatically send email to customer and administrator with attached PDF in active locale
        $currentLocale = in_array(app()->getLocale(), ['es', 'en'], true) ? app()->getLocale() : 'es';

        try {
            $pdfInstance = app(InvoicePdfService::class)->generatePdf($purchaseRequest, $currentLocale);
            $pdfOutput = $pdfInstance->output();
            $pdfFilename = "SpeedShopper_Factura_{$purchaseRequest->number}_{$currentLocale}.pdf";

            $mail = new InvoiceCreatedMail(
                purchaseRequest: $purchaseRequest,
                locale: $currentLocale,
                pdfOutput: $pdfOutput,
                pdfFilename: $pdfFilename,
            );

            $adminEmail = Setting::get('admin_notification_email') ?: config('mail.from.address');
            $customerEmail = $purchaseRequest->customer?->email;

            if ($customerEmail) {
                $pendingMail = Mail::to($customerEmail);
                if ($adminEmail && strtolower((string) $adminEmail) !== strtolower((string) $customerEmail)) {
                    $pendingMail->bcc($adminEmail);
                }
                $pendingMail->send($mail);
            } elseif ($adminEmail) {
                Mail::to($adminEmail)->send($mail);
            }
        } catch (\Throwable $e) {
            Log::warning('Could not send automated invoice email: '.$e->getMessage());
        }

        $this->showCreateForm = false;
        $this->swalSuccess(__('Factura :number creada correctamente con sus tarifas organizadas.', ['number' => $purchaseRequest->number]));
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
