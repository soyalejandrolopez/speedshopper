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
use App\Services\FinanceService;
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

    public array $selectedServices = ['personal_shopper'];

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

    /** @return array<string, array{key: string, title: string, subtitle: string, icon: string}> */
    public function serviceDefinitions(): array
    {
        return service_definitions();
    }

    public function isServiceActive(string $key): bool
    {
        $map = [
            'shopper' => 'personal_shopper',
            'online' => 'online_shopping',
            'repack' => 'repack',
            'personal_shopper' => 'personal_shopper',
            'online_shopping' => 'online_shopping',
        ];
        $normalizedKey = $map[$key] ?? $key;

        return in_array($normalizedKey, $this->selectedServices, true);
    }

    public function toggleService(string $key): void
    {
        $map = [
            'shopper' => 'personal_shopper',
            'online' => 'online_shopping',
            'repack' => 'repack',
            'personal_shopper' => 'personal_shopper',
            'online_shopping' => 'online_shopping',
        ];
        $normalizedKey = $map[$key] ?? $key;

        if (in_array($normalizedKey, $this->selectedServices, true)) {
            if (count($this->selectedServices) > 1) {
                $this->selectedServices = array_values(array_diff($this->selectedServices, [$normalizedKey]));
            }
        } else {
            $this->selectedServices[] = $normalizedKey;
        }

        $this->syncServiceTypeState();
    }

    public function setServiceType(string $type): void
    {
        $this->serviceType = in_array($type, ['shopper', 'online', 'repack'], true) ? $type : 'shopper';

        if ($this->serviceType === 'online') {
            $this->selectedServices = ['online_shopping'];
        } elseif ($this->serviceType === 'repack') {
            $this->selectedServices = ['repack'];
        } else {
            $this->selectedServices = ['personal_shopper'];
        }

        $this->syncServiceTypeState();
    }

    public function syncServiceTypeState(): void
    {
        $hasShopper = in_array('personal_shopper', $this->selectedServices, true);
        $hasOnline = in_array('online_shopping', $this->selectedServices, true);
        $hasRepack = in_array('repack', $this->selectedServices, true);

        if ($hasShopper && ! $hasOnline && ! $hasRepack) {
            $this->serviceType = 'shopper';
        } elseif ($hasOnline && ! $hasShopper && ! $hasRepack) {
            $this->serviceType = 'online';
        } elseif ($hasRepack && ! $hasShopper && ! $hasOnline) {
            $this->serviceType = 'repack';
        } else {
            $this->serviceType = $hasShopper ? 'shopper' : ($hasOnline ? 'online' : 'repack');
        }

        if ($hasShopper) {
            $this->guidedQuestions['apply_shopper_commission'] = true;
        } else {
            $this->guidedQuestions['apply_shopper_commission'] = false;
        }

        if ($hasOnline) {
            $this->guidedQuestions['apply_warehouse_commission'] = true;
            $this->guidedQuestions['warehouse_delivery_count'] = max(1, (int) ($this->guidedQuestions['warehouse_delivery_count'] ?? 0));
        } else {
            $this->guidedQuestions['apply_warehouse_commission'] = false;
        }

        if ($hasRepack) {
            $this->guidedQuestions['warehouse_delivery_count'] = max(1, (int) ($this->guidedQuestions['warehouse_delivery_count'] ?? 0));
        }

        if (! $hasOnline && ! $hasRepack && ! $hasShopper) {
            $this->guidedQuestions['warehouse_delivery_count'] = 0;
        }

        $this->syncGuidedQuestionsToCosts();
    }

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
        $this->selectedServices = ['personal_shopper'];

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

    public function editInvoice(int $requestId): void
    {
        $this->resetValidation();
        $this->rates = app(PricingRateService::class)->getRates();

        $request = PurchaseRequest::with(['customer', 'costItems'])->findOrFail($requestId);

        $this->isEditing = true;
        $this->editingRequestId = $request->id;
        $this->editingRequestNumber = $request->number;

        // Detect services from request or cost items
        $services = [];
        if (! empty($request->services) && is_array($request->services)) {
            foreach ($request->services as $s) {
                if (in_array($s, ['personal_shopper', 'online_shopping', 'repack'], true)) {
                    $services[] = $s;
                } elseif ($s === 'shopper') {
                    $services[] = 'personal_shopper';
                } elseif ($s === 'online') {
                    $services[] = 'online_shopping';
                }
            }
        }

        if (empty($services)) {
            $hasReceivingCommission = $request->costItems->where('type', CostType::ReceivingFee)->filter(fn ($c) => stripos($c->description, 'Comisión') !== false)->isNotEmpty();
            $hasShopperFee = $request->costItems->where('type', CostType::ShopperFee)->isNotEmpty();
            $hasBoxesOrDelivery = $request->costItems->whereIn('type', [CostType::PackingFee, CostType::ReceivingFee])->isNotEmpty();
            $productCosts = $request->costItems->where('type', CostType::ProductCost);
            $productTotal = (float) $productCosts->sum('amount');

            if ($hasReceivingCommission) {
                $services[] = 'online_shopping';
            }
            if ($hasShopperFee || ($productTotal > 0 && ! $hasReceivingCommission)) {
                $services[] = 'personal_shopper';
            }
            if ($hasBoxesOrDelivery) {
                $services[] = 'repack';
            }
            if (empty($services)) {
                $services = ['personal_shopper'];
            }
        }

        $this->selectedServices = array_values(array_unique($services));

        // Populate items
        $items = [];
        $productCosts = $request->costItems->where('type', CostType::ProductCost);
        if ($productCosts->isNotEmpty()) {
            foreach ($productCosts as $cost) {
                // Extract price if format contains "(Comprado online - Pagado en internet: $XX.XX)"
                $unitPrice = (float) $cost->amount;
                if ((! $this->isServiceActive('personal_shopper')) && preg_match('/Pagado en internet:\s*\$([0-9\.,]+)/i', $cost->description ?? '', $m)) {
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

        // Initialize guided questions based on active services
        $this->guidedQuestions = [
            'apply_shopper_commission' => $this->isServiceActive('personal_shopper'),
            'extra_stores_count' => 0,
            'boxes_small_count' => 0,
            'boxes_medium_count' => 0,
            'boxes_large_count' => 0,
            'apply_warehouse_commission' => $this->isServiceActive('online_shopping'),
            'warehouse_delivery_count' => ($this->isServiceActive('online_shopping') || $this->isServiceActive('repack')) ? 1 : 0,
            'storage_months_count' => 0,
        ];
        $this->customCosts = [];

        $nonProductCosts = $request->costItems->where('type', '!=', CostType::ProductCost);

        foreach ($nonProductCosts as $cost) {
            $desc = $cost->description ?? '';
            $amount = (float) $cost->amount;

            if (stripos($desc, 'Personal Shopper') !== false || stripos($desc, 'Shopper') !== false || ($cost->type === CostType::ShopperFee && stripos($desc, 'Comisión') !== false)) {
                $this->guidedQuestions['apply_shopper_commission'] = true;
            } elseif (stripos($desc, 'Tienda Adicional') !== false || stripos($desc, 'Additional Store') !== false || stripos($desc, 'Extra Store') !== false) {
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
            } elseif (stripos($desc, 'Comisión Almacén') !== false || stripos($desc, 'Warehouse Commission') !== false || stripos($desc, 'Warehouse / Online') !== false || stripos($desc, 'Online Shopping Commission') !== false) {
                $this->guidedQuestions['apply_warehouse_commission'] = true;
            } elseif (stripos($desc, 'Llevar') !== false || stripos($desc, 'Traslado') !== false || stripos($desc, 'Delivery') !== false || stripos($desc, 'Drop-off') !== false || stripos($desc, 'Transfer') !== false) {
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

        if ($request->status === RequestStatus::AwaitingPayment) {
            $this->invoiceType = 'pendiente';
        } elseif (in_array($request->status, [RequestStatus::Purchased, RequestStatus::InTransit, RequestStatus::Received, RequestStatus::Packing, RequestStatus::Ready, RequestStatus::Shipped, RequestStatus::Delivered], true)) {
            $this->invoiceType = 'pagado';
        } else {
            // Default to 'cotizacion' (Cotización / Presupuesto informativo).
            $this->invoiceType = 'cotizacion';
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

        $this->syncServiceTypeState();
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
        $hasShopper = $this->isServiceActive('personal_shopper');
        $hasOnline = $this->isServiceActive('online_shopping');
        $hasRepack = $this->isServiceActive('repack');

        // 1. Comisión Personal Shopper (solo si Personal Shopper está activo)
        if ($hasShopper && ! empty($this->guidedQuestions['apply_shopper_commission'])) {
            $costs[] = [
                'preset' => 'shopper_commission',
                'type' => 'shopper_fee',
                'description' => __('Comisión Personal Shopper (:percent% - :stores tiendas / :hours hrs)', [
                    'percent' => $calc['percent'],
                    'stores' => $calc['stores'],
                    'hours' => $calc['hours'],
                ]),
                'amount' => $calc['amount'],
            ];
        }

        // 2. Tiendas adicionales (solo compras físicas)
        $extraStores = (int) ($this->guidedQuestions['extra_stores_count'] ?? 0);
        if ($hasShopper && $extraStores > 0) {
            $rate = (float) ($this->rates['extra_store_fee'] ?? 20.0);
            $costs[] = [
                'preset' => 'extra_store',
                'type' => 'shopper_fee',
                'description' => __('Visita a Tienda Adicional (:count x $:rate)', [
                    'count' => $extraStores,
                    'rate' => number_format($rate, 2),
                ]),
                'amount' => round($extraStores * $rate, 2),
            ];
        }

        // 3. Cajas Heavy Duty (aplica a cualquier servicio de reempaque o cajas agregadas)
        $boxSmall = (int) ($this->guidedQuestions['boxes_small_count'] ?? 0);
        if ($boxSmall > 0) {
            $rate = (float) ($this->rates['box_small_heavy_duty'] ?? 15.0);
            $costs[] = [
                'preset' => 'box_small',
                'type' => 'packing_fee',
                'description' => __('1 Caja Small Heavy Duty (:count x $:rate)', [
                    'count' => $boxSmall,
                    'rate' => number_format($rate, 2),
                ]),
                'amount' => round($boxSmall * $rate, 2),
            ];
        }

        $boxMed = (int) ($this->guidedQuestions['boxes_medium_count'] ?? 0);
        if ($boxMed > 0) {
            $rate = (float) ($this->rates['box_medium_heavy_duty'] ?? 20.0);
            $costs[] = [
                'preset' => 'box_medium',
                'type' => 'packing_fee',
                'description' => __('1 Caja Mediana Heavy Duty (:count x $:rate)', [
                    'count' => $boxMed,
                    'rate' => number_format($rate, 2),
                ]),
                'amount' => round($boxMed * $rate, 2),
            ];
        }

        $boxLarge = (int) ($this->guidedQuestions['boxes_large_count'] ?? 0);
        if ($boxLarge > 0) {
            $rate = (float) ($this->rates['box_large_heavy_duty'] ?? 25.0);
            $costs[] = [
                'preset' => 'box_large',
                'type' => 'packing_fee',
                'description' => __('1 Caja Larga Heavy Duty (:count x $:rate)', [
                    'count' => $boxLarge,
                    'rate' => number_format($rate, 2),
                ]),
                'amount' => round($boxLarge * $rate, 2),
            ];
        }

        // 4. Comisión Almacén / Compras Online (15% sobre el valor del pedido online)
        if ($hasOnline || (! $hasRepack && ! empty($this->guidedQuestions['apply_warehouse_commission']))) {
            $pct = (float) ($this->rates['warehouse_percent'] ?? 15.0);
            $costs[] = [
                'preset' => 'warehouse_commission',
                'type' => 'receiving_fee',
                'description' => __('Comisión Almacén / Compras Online (:percent% sobre $:subtotal)', [
                    'percent' => $pct,
                    'subtotal' => number_format($subtotal, 2),
                ]),
                'amount' => round($subtotal * ($pct / 100), 2),
            ];
        }

        // 5. Traslado de caja al Almacén (Fijo $20 en compras online y reempaque)
        $delivery = (int) ($this->guidedQuestions['warehouse_delivery_count'] ?? 0);
        if (($hasOnline || $hasRepack) && $delivery === 0) {
            $delivery = 1;
            $this->guidedQuestions['warehouse_delivery_count'] = 1;
        }

        if ($delivery > 0) {
            $rate = (float) ($this->rates['warehouse_delivery_fee'] ?? 20.0);
            $costs[] = [
                'preset' => 'warehouse_delivery',
                'type' => 'receiving_fee',
                'description' => __('Servicio de Traslado de Caja al Almacén (Fijo $:rate:multiplier)', [
                    'rate' => number_format($rate, 2),
                    'multiplier' => $delivery > 1 ? " x {$delivery}" : '',
                ]),
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
                'description' => __('Almacenaje en Almacén (:months mes(es) tras 30 días)', [
                    'months' => $storageMonths,
                ]),
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
        // En compras online o reempaque sin personal shopper, lo que el cliente pagó en internet no se suma al total de factura
        $hasShopper = $this->isServiceActive('personal_shopper');
        $itemsTotal = $hasShopper ? $this->productsSubtotal : 0.0;
        $costsTotal = 0.0;
        foreach ($this->invoiceForm['costs'] as $cost) {
            $costsTotal += (float) ($cost['amount'] ?? 0);
        }

        return (float) ($itemsTotal + $costsTotal);
    }

    public function getInvoicedEarningsProperty(): float
    {
        $hasShopper = $this->isServiceActive('personal_shopper');
        // Si no incluye Personal Shopper (solo online o repack), todo lo facturado son comisiones / servicios de la empresa
        if (! $hasShopper) {
            return (float) $this->invoicedTotal;
        }

        // Si incluye Personal Shopper, la ganancia de la empresa son los cargos por servicio (comisiones, cajas, tiendas, etc.)
        $earnings = 0.0;
        foreach ($this->invoiceForm['costs'] as $cost) {
            $type = $cost['type'] ?? '';
            if ($type !== CostType::ProductCost->value) {
                $earnings += (float) ($cost['amount'] ?? 0);
            }
        }

        return (float) $earnings;
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

        $hasShopper = $this->isServiceActive('personal_shopper');

        if ($this->isEditing && $this->editingRequestId) {
            $purchaseRequest = PurchaseRequest::findOrFail($this->editingRequestId);

            $purchaseRequest->update([
                'customer_id' => $this->invoiceForm['customer_id'],
                'product_name' => count($this->invoiceForm['items']) > 1 ? $allProductsSummary : $firstItem['product_name'],
                'store' => $firstItem['store'] ?? null,
                'quantity' => $firstItem['quantity'] ?? 1,
                'unit_price' => $hasShopper ? ($firstItem['unit_price'] ?? null) : 0.0,
                'services' => $this->selectedServices,
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

                if ($hasShopper) {
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
                    // Online purchase / repack: $0 billable amount
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

            // Sync payments if updated
            $totalInvoiced = $this->invoicedTotal;
            $amountPaid = (float) ($this->invoiceForm['amount_paid'] ?? 0);
            if ($targetStatus === RequestStatus::Purchased && $amountPaid < $totalInvoiced) {
                $amountPaid = $totalInvoiced;
            }
            $alreadyPaid = (float) Payment::where('billable_type', PurchaseRequest::class)
                ->where('billable_id', $purchaseRequest->id)
                ->sum('amount_paid');

            if ($amountPaid > $alreadyPaid) {
                Payment::create([
                    'customer_id' => $purchaseRequest->customer_id,
                    'billable_type' => PurchaseRequest::class,
                    'billable_id' => $purchaseRequest->id,
                    'invoice_total' => $totalInvoiced,
                    'amount_paid' => $amountPaid - $alreadyPaid,
                    'payment_method' => PaymentMethod::tryFrom($this->invoiceForm['payment_method']) ?? PaymentMethod::Zelle,
                    'reference' => $this->invoiceForm['payment_reference'] ?? null,
                    'paid_at' => $this->invoiceForm['paid_at'] ? now()->parse($this->invoiceForm['paid_at']) : now(),
                    'notes' => 'Pago registrado al actualizar factura '.$purchaseRequest->number,
                ]);
            }

            $purchaseRequest->load(['customer', 'costItems']);

            // Automatically send updated email to customer and administrator
            $this->sendInvoiceNotificationMail($purchaseRequest, isUpdate: true);

            $this->showCreateForm = false;
            $this->isEditing = false;
            $this->editingRequestId = null;
            $this->editingRequestNumber = null;
            $this->swalSuccess(__('Factura :number actualizada y notificada por correo.', ['number' => $purchaseRequest->number]));

            return;
        }

        $purchaseRequest = PurchaseRequest::create([
            'customer_id' => $this->invoiceForm['customer_id'],
            'product_name' => count($this->invoiceForm['items']) > 1 ? $allProductsSummary : $firstItem['product_name'],
            'store' => $firstItem['store'] ?? null,
            'quantity' => $firstItem['quantity'] ?? 1,
            'unit_price' => $hasShopper ? ($firstItem['unit_price'] ?? null) : 0.0,
            'services' => $this->selectedServices,
            'status' => $targetStatus,
            'notes' => $this->invoiceForm['notes'] ?? null,
        ]);

        // Add product cost items
        foreach ($this->invoiceForm['items'] as $item) {
            $qty = (int) ($item['quantity'] ?? 1);
            $price = (float) ($item['unit_price'] ?? 0);
            $subtotal = $qty * $price;

            if ($hasShopper) {
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
        if ($targetStatus === RequestStatus::Purchased && $amountPaid < $totalInvoiced) {
            $amountPaid = $totalInvoiced;
        }

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

        $purchaseRequest->load(['customer', 'costItems']);

        // Automatically send email to customer and administrator with attached PDF in active locale
        $this->sendInvoiceNotificationMail($purchaseRequest, isUpdate: false);

        $this->showCreateForm = false;
        $this->swalSuccess(__('Factura :number creada correctamente con sus tarifas organizadas.', ['number' => $purchaseRequest->number]));
    }

    protected function sendInvoiceNotificationMail(PurchaseRequest $purchaseRequest, bool $isUpdate = false): void
    {
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
                isUpdate: $isUpdate,
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
            'paymentForm.amount_paid' => [
                'required',
                'numeric',
                'min:0.01',
                'max:'.($this->paymentForm['pending_balance'] ?? 0),
            ],
            'paymentForm.payment_method' => ['required', 'string'],
        ], [
            'paymentForm.amount_paid.max' => __('El monto pagado no puede exceder el balance pendiente (:max).', ['max' => money($this->paymentForm['pending_balance'] ?? 0)]),
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

        $finance = app(FinanceService::class);

        return view('livewire.admin.billing.billing-index', [
            'requests' => $requests,
            'paymentsByRequest' => $paymentsByRequest,
            'totalInvoiced' => $finance->getRequestsTotalInvoiced(),
            'totalEarnings' => $finance->getRequestsTotalEarnings(),
            'totalCollected' => $finance->getRequestsTotalCollected(),
            'totalPending' => $finance->getRequestsTotalBalanceDue(),
            'customers' => Customer::orderBy('name')->get(['id', 'name', 'phone', 'email', 'country']),
        ]);
    }
}
