<?php

namespace App\Livewire;

use App\Enums\CostType;
use App\Models\CostItem;
use App\Models\Customer;
use App\Models\PurchaseRequest;
use App\Models\User;
use App\Services\AdminNotifier;
use App\Services\PricingRateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ClientRegistrationForm extends Component
{
    public int $step = 1;

    public bool $sent = false;

    public array $form = [
        'name' => '',
        'whatsapp' => '',
        'email' => '',
        'country' => 'VE',
        'city' => '',
        'address' => '',
        'create_account' => false,
        'password' => '',
        'password_confirmation' => '',
        'services' => [],
        'products' => '',
        'preferred_stores' => '',
        'budget' => '',
        'has_links' => 'no',
        'product_links' => '',
        'find_deals' => 'no',
        'already_purchased' => 'no',
        'store_name' => '',
        'order_number' => '',
        'tracking_number' => '',
        'approx_packages' => '',
        'boxes_small' => 0,
        'boxes_medium' => 0,
        'boxes_large' => 0,
        'courier' => 'no',
        'courier_name' => '',
        'need_shipping_coordination' => 'no',
        'comments' => '',
        'confirm_correct' => false,
        'accept_costs' => false,
        'accept_contact' => false,
    ];

    public const TOTAL_STEPS = 3;

    public function mount(): void
    {
        $this->fillAuthenticatedData();
    }

    public function fillAuthenticatedData(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            $customer = $user->customer;

            $this->form['name'] = $this->form['name'] ?: ($user->name ?? $customer?->name ?? '');
            $this->form['email'] = $this->form['email'] ?: ($user->email ?? $customer?->email ?? '');
            $this->form['whatsapp'] = $this->form['whatsapp'] ?: ($customer?->whatsapp ?? $user->whatsapp ?? $user->phone ?? '');
            $this->form['country'] = $customer?->country ?? $user->country ?? ($this->form['country'] ?: 'VE');
            $this->form['city'] = $this->form['city'] ?: ($customer?->city ?? $user->city ?? '');
            $this->form['address'] = $this->form['address'] ?: ($customer?->address ?? $user->address ?? '');
        }
    }

    /** @return array<string, array{key: string, title: string, subtitle: string, icon: string}> */
    public function serviceDefinitions(): array
    {
        return service_definitions();
    }

    /** @return array<int, string> */
    public function serviceOptions(): array
    {
        return service_options();
    }

    /** @return array<string, string> */
    public function countries(): array
    {
        return collect(countries_served_list())
            ->mapWithKeys(fn ($code) => [$code => country_name($code)])
            ->all();
    }

    public function getRatesProperty(): array
    {
        return app(PricingRateService::class)->getRates();
    }

    public function getPackagingTotalProperty(): float
    {
        $rates = $this->rates;
        $smallRate = (float) ($rates['box_small_heavy_duty'] ?? 15.0);
        $mediumRate = (float) ($rates['box_medium_heavy_duty'] ?? 20.0);
        $largeRate = (float) ($rates['box_large_heavy_duty'] ?? 25.0);

        $small = max(0, (int) ($this->form['boxes_small'] ?? 0));
        $med = max(0, (int) ($this->form['boxes_medium'] ?? 0));
        $large = max(0, (int) ($this->form['boxes_large'] ?? 0));

        return ($small * $smallRate) + ($med * $mediumRate) + ($large * $largeRate);
    }

    public function incrementBox(string $type): void
    {
        $key = 'boxes_'.$type;
        if (array_key_exists($key, $this->form)) {
            $this->form[$key] = min(9999, ((int) ($this->form[$key] ?? 0)) + 1);
        }
    }

    public function decrementBox(string $type): void
    {
        $key = 'boxes_'.$type;
        if (array_key_exists($key, $this->form)) {
            $this->form[$key] = max(0, ((int) ($this->form[$key] ?? 0)) - 1);
        }
    }

    public function selectService(string $key): void
    {
        $current = $this->form['services'];

        if (in_array($key, $current, true)) {
            $this->form['services'] = array_values(array_diff($current, [$key]));
        } else {
            $this->form['services'][] = $key;
        }
    }

    public function next(): void
    {
        $this->validateStep($this->step);
        $this->step++;
    }

    public function back(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function submit(): void
    {
        $this->validate();

        $customer = Customer::firstOrCreate(
            ['email' => $this->form['email']],
            [
                'name' => $this->form['name'],
                'whatsapp' => $this->form['whatsapp'] ?: null,
                'country' => $this->form['country'] ?: null,
                'city' => $this->form['city'] ?: null,
                'address' => $this->form['address'] ?: null,
                'registered_at' => now(),
            ]
        );

        $customer->update([
            'name' => $this->form['name'],
            'whatsapp' => $this->form['whatsapp'] ?: $customer->whatsapp,
            'country' => $this->form['country'] ?: $customer->country,
            'city' => $this->form['city'] ?: $customer->city,
            'address' => $this->form['address'] ?: $customer->address,
        ]);

        if ($this->form['create_account'] && ! empty($this->form['password'])) {
            $user = User::where('email', $this->form['email'])->first();
            if (! $user) {
                $user = User::create([
                    'name' => $this->form['name'],
                    'email' => $this->form['email'],
                    'password' => Hash::make($this->form['password']),
                    'whatsapp' => $this->form['whatsapp'] ?: null,
                    'country' => $this->form['country'] ?: null,
                ]);
                $user->assignRole('client');
            }
            $customer->update(['user_id' => $user->id]);

            if (! Auth::check()) {
                Auth::login($user);
            }
        } elseif (Auth::check()) {
            $customer->update(['user_id' => Auth::id()]);
        }

        $smallCount = max(0, (int) ($this->form['boxes_small'] ?? 0));
        $mediumCount = max(0, (int) ($this->form['boxes_medium'] ?? 0));
        $largeCount = max(0, (int) ($this->form['boxes_large'] ?? 0));

        $services = $this->form['services'];
        if (($smallCount > 0 || $mediumCount > 0 || $largeCount > 0) && ! in_array('repack', $services, true) && ! in_array('packing', $services, true)) {
            $services[] = 'repack';
            $services[] = 'packing';
        }

        $budget = ! empty($this->form['budget']) ? (float) $this->form['budget'] : null;

        $request = $customer->purchaseRequests()->create([
            'product_name' => mb_substr($this->form['products'] ?: __('Service request'), 0, 255),
            'store' => $this->form['preferred_stores'] ?: null,
            'services' => $services,
            'description' => $this->buildDescription(),
            'status' => 'new',
            'quantity' => 1,
            'unit_price' => $budget,
        ]);

        $rates = $this->rates;
        $smallRate = (float) ($rates['box_small_heavy_duty'] ?? 15.0);
        $mediumRate = (float) ($rates['box_medium_heavy_duty'] ?? 20.0);
        $largeRate = (float) ($rates['box_large_heavy_duty'] ?? 25.0);
        $deliveryRate = (float) ($rates['warehouse_delivery_fee'] ?? 20.0);

        // 1. Personal Shopper Cost Items (Compras en tiendas físicas)
        if (in_array('personal_shopper', $services, true) || empty($services)) {
            if ($budget !== null && $budget > 0) {
                CostItem::create([
                    'costable_type' => PurchaseRequest::class,
                    'costable_id' => $request->id,
                    'type' => CostType::ProductCost,
                    'description' => 'Valor de Productos / Presupuesto Cliente',
                    'amount' => $budget,
                ]);

                // Calculate commission percent based on tiers (20% for < $700, 15% for >= $700)
                $percent = $budget >= 700 ? 15.0 : 20.0;
                $commAmount = round($budget * ($percent / 100), 2);
                CostItem::create([
                    'costable_type' => PurchaseRequest::class,
                    'costable_id' => $request->id,
                    'type' => CostType::ShopperFee,
                    'description' => "Comisión Personal Shopper ({$percent}%)",
                    'amount' => $commAmount,
                ]);
            }
        }

        // 2. Comprar Online Cost Items (Cliente compra por su cuenta en tiendas online)
        if (in_array('online_shopping', $services, true)) {
            if ($budget !== null && $budget > 0) {
                CostItem::create([
                    'costable_type' => PurchaseRequest::class,
                    'costable_id' => $request->id,
                    'type' => CostType::ProductCost,
                    'description' => 'Valor Pagado en Internet: $'.number_format($budget, 2).' (No se cobra en factura)',
                    'amount' => 0.0,
                ]);

                $onlinePercent = (float) ($rates['warehouse_percent'] ?? 15.0);
                $commAmount = round($budget * ($onlinePercent / 100), 2);
                CostItem::create([
                    'costable_type' => PurchaseRequest::class,
                    'costable_id' => $request->id,
                    'type' => CostType::ReceivingFee,
                    'description' => "Comisión Almacén / Compras Online ({$onlinePercent}% sobre $".number_format($budget, 2).')',
                    'amount' => $commAmount,
                ]);
            }

            // Traslado fijo $20
            CostItem::create([
                'costable_type' => PurchaseRequest::class,
                'costable_id' => $request->id,
                'type' => CostType::ReceivingFee,
                'description' => 'Servicio de Traslado de Caja al Almacén (Fijo $'.number_format($deliveryRate, 2).')',
                'amount' => $deliveryRate,
            ]);
        }

        // 3. Reempaque Cost Items
        if (in_array('repack', $services, true)) {
            // Traslado fijo $20 si no se agregó ya por compras online
            if (! in_array('online_shopping', $services, true)) {
                CostItem::create([
                    'costable_type' => PurchaseRequest::class,
                    'costable_id' => $request->id,
                    'type' => CostType::ReceivingFee,
                    'description' => 'Servicio de Traslado de Caja al Almacén (Fijo $'.number_format($deliveryRate, 2).')',
                    'amount' => $deliveryRate,
                ]);
            }
        }

        // Cajas Heavy Duty (Small $15, Med $20, Larga $25)
        if ($smallCount > 0) {
            CostItem::create([
                'costable_type' => PurchaseRequest::class,
                'costable_id' => $request->id,
                'type' => CostType::PackingFee,
                'description' => "1 Caja Small Heavy Duty ({$smallCount} x $".number_format($smallRate, 2).')',
                'amount' => $smallCount * $smallRate,
            ]);
        }

        if ($mediumCount > 0) {
            CostItem::create([
                'costable_type' => PurchaseRequest::class,
                'costable_id' => $request->id,
                'type' => CostType::PackingFee,
                'description' => "1 Caja Mediana Heavy Duty ({$mediumCount} x $".number_format($mediumRate, 2).')',
                'amount' => $mediumCount * $mediumRate,
            ]);
        }

        if ($largeCount > 0) {
            CostItem::create([
                'costable_type' => PurchaseRequest::class,
                'costable_id' => $request->id,
                'type' => CostType::PackingFee,
                'description' => "1 Caja Larga Heavy Duty ({$largeCount} x $".number_format($largeRate, 2).')',
                'amount' => $largeCount * $largeRate,
            ]);
        }

        $package = null;

        if ($this->form['already_purchased'] === 'yes') {
            $package = $customer->packages()->create([
                'purchase_request_id' => $request->id,
                'store' => $this->form['store_name'] ?: null,
                'original_tracking' => $this->form['tracking_number'] ?: null,
                'status' => 'received',
            ]);
        }

        $needsShipment = in_array('delivery_to_courier', $this->form['services'], true)
            || $this->form['courier'] === 'yes'
            || $this->form['need_shipping_coordination'] === 'yes';

        if ($needsShipment) {
            $shipment = $customer->shipments()->create([
                'carrier' => $this->form['courier_name'] ?: null,
                'destination_country' => $this->form['country'] ?: null,
                'status' => 'draft',
            ]);

            if ($package) {
                $shipment->packages()->attach($package->id);
            }
        }

        AdminNotifier::notifyNewPurchaseRequest($request);

        $this->sent = true;
    }

    public function resetForm(): void
    {
        $this->reset();
        $this->fillAuthenticatedData();
    }

    public function progressPercent(): int
    {
        if ($this->sent) {
            return 100;
        }

        return (int) round((($this->step - 1) / self::TOTAL_STEPS) * 100);
    }

    public function validateStep(int $step): void
    {
        $fields = match ($step) {
            1 => [
                'form.name', 'form.whatsapp', 'form.email', 'form.country', 'form.city', 'form.address', 'form.services', 'form.create_account',
                'form.password', 'form.password_confirmation',
            ],
            2 => [
                'form.products', 'form.preferred_stores', 'form.budget', 'form.has_links',
                'form.product_links', 'form.find_deals', 'form.already_purchased', 'form.store_name',
                'form.order_number', 'form.tracking_number', 'form.approx_packages',
                'form.boxes_small', 'form.boxes_medium', 'form.boxes_large',
            ],
            default => [
                'form.courier', 'form.courier_name', 'form.need_shipping_coordination',
                'form.comments', 'form.confirm_correct', 'form.accept_costs', 'form.accept_contact',
            ],
        };

        $rules = array_intersect_key($this->rules(), array_flip($fields));

        $this->validate($rules);
    }

    /** @return array<string, array<int, mixed>> */
    protected function rules(): array
    {
        $hasPassword = ! empty($this->form['password']);

        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.whatsapp' => ['required', 'string', 'max:20'],
            'form.email' => ['required', 'email', 'max:255'],
            'form.country' => ['required', 'string', 'max:50'],
            'form.city' => ['nullable', 'string', 'max:255'],
            'form.address' => ['nullable', 'string', 'max:500'],
            'form.create_account' => ['nullable', 'boolean'],
            'form.password' => [$hasPassword ? 'required' : 'nullable', 'string', 'min:4', 'same:form.password_confirmation'],
            'form.password_confirmation' => [$hasPassword ? 'required' : 'nullable', 'string', 'min:4'],
            'form.services' => ['required', 'array', 'min:1'],
            'form.services.*' => ['string'],
            'form.products' => ['required', 'string', 'max:2000'],
            'form.preferred_stores' => ['nullable', 'string', 'max:255'],
            'form.budget' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'form.has_links' => ['required', 'in:yes,no'],
            'form.product_links' => ['nullable', 'string', 'max:2000'],
            'form.find_deals' => ['required', 'in:yes,no'],
            'form.already_purchased' => ['nullable', 'in:yes,no'],
            'form.store_name' => ['nullable', 'string', 'max:255'],
            'form.order_number' => ['nullable', 'string', 'max:255'],
            'form.tracking_number' => ['nullable', 'string', 'max:255'],
            'form.approx_packages' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'form.boxes_small' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'form.boxes_medium' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'form.boxes_large' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'form.courier' => ['nullable', 'in:yes,no'],
            'form.courier_name' => ['nullable', 'string', 'max:255'],
            'form.need_shipping_coordination' => ['nullable', 'in:yes,no'],
            'form.comments' => ['nullable', 'string', 'max:2000'],
            'form.confirm_correct' => ['accepted'],
            'form.accept_costs' => ['accepted'],
            'form.accept_contact' => ['accepted'],
        ];
    }

    protected function buildDescription(): string
    {
        $f = $this->form;
        $options = $this->serviceOptions();
        $lines = [];

        $lines[] = __('Services').': '.(count($f['services'])
            ? implode(', ', array_map(fn ($s) => $options[$s] ?? $s, $f['services']))
            : '—');
        $lines[] = __('Products').': '.($f['products'] ?: '—');
        $lines[] = __('Preferred stores').': '.($f['preferred_stores'] ?: '—');
        $lines[] = __('Budget').': '.($f['budget'] !== '' ? money((float) $f['budget']) : '—');
        $lines[] = __('Has product links').': '.($f['has_links'] === 'yes' ? __('Yes') : __('No'));
        if ($f['has_links'] === 'yes' && $f['product_links']) {
            $lines[] = __('Links').': '.$f['product_links'];
        }
        $lines[] = __('Find deals').': '.($f['find_deals'] === 'yes' ? __('Yes') : __('No'));

        $smallCount = max(0, (int) ($f['boxes_small'] ?? 0));
        $mediumCount = max(0, (int) ($f['boxes_medium'] ?? 0));
        $largeCount = max(0, (int) ($f['boxes_large'] ?? 0));
        if ($smallCount > 0 || $mediumCount > 0 || $largeCount > 0) {
            $boxParts = [];
            if ($smallCount > 0) {
                $boxParts[] = "{$smallCount}x Small";
            }
            if ($mediumCount > 0) {
                $boxParts[] = "{$mediumCount}x Mediana";
            }
            if ($largeCount > 0) {
                $boxParts[] = "{$largeCount}x Larga";
            }
            $lines[] = __('Embalaje / Cajas').': '.implode(', ', $boxParts).' ('.__('Total').': '.money($this->packagingTotal).')';
        }

        if ($f['already_purchased'] === 'yes') {
            $lines[] = __('Already purchased').': '.__('Yes');
            $lines[] = __('Store').': '.($f['store_name'] ?: '—');
            $lines[] = __('Order number').': '.($f['order_number'] ?: '—');
            $lines[] = __('Tracking').': '.($f['tracking_number'] ?: '—');
            $lines[] = __('Approx packages').': '.($f['approx_packages'] ?: '—');
        } else {
            $lines[] = __('Already purchased').': '.__('No');
        }

        $lines[] = __('Preferred courier').': '.($f['courier'] === 'yes' ? ($f['courier_name'] ?: __('Yes')) : __('No'));
        $lines[] = __('Coordinate shipping').': '.($f['need_shipping_coordination'] === 'yes' ? __('Yes') : __('No'));

        if ($f['comments']) {
            $lines[] = __('Comments').': '.$f['comments'];
        }

        return implode("\n", $lines);
    }

    public function render()
    {
        return view('livewire.client-registration-form');
    }
}
