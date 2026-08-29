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

class PublicRequestForm extends Component
{
    public array $form = [
        'name' => '',
        'email' => '',
        'whatsapp' => '',
        'create_account' => false,
        'password' => '',
        'password_confirmation' => '',
    ];

    /** @var array<int, array{product_name: string, product_url: string, description: string, quantity: int}> */
    public array $items = [];

    public int $boxes_small = 0;

    public int $boxes_medium = 0;

    public int $boxes_large = 0;

    public bool $sent = false;

    public int $createdCount = 0;

    public function mount(): void
    {
        $this->items = [
            $this->emptyItem(),
        ];
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

        return ($this->boxes_small * $smallRate) + ($this->boxes_medium * $mediumRate) + ($this->boxes_large * $largeRate);
    }

    public function incrementBox(string $type): void
    {
        $prop = 'boxes_'.$type;
        if (property_exists($this, $prop)) {
            $this->$prop++;
        }
    }

    public function decrementBox(string $type): void
    {
        $prop = 'boxes_'.$type;
        if (property_exists($this, $prop)) {
            $this->$prop = max(0, $this->$prop - 1);
        }
    }

    public function addItem(): void
    {
        $this->items[] = $this->emptyItem();
    }

    public function removeItem(int $index): void
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    /** @return array{product_name: string, product_url: string, description: string, quantity: int} */
    protected function emptyItem(): array
    {
        return [
            'product_name' => '',
            'product_url' => '',
            'description' => '',
            'quantity' => 1,
        ];
    }

    protected function rules(): array
    {
        $hasAccountCreation = ! empty($this->form['create_account']);

        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.email' => ['required', 'email', 'max:255'],
            'form.whatsapp' => ['nullable', 'string', 'max:50'],
            'form.create_account' => ['nullable', 'boolean'],
            'form.password' => [$hasAccountCreation ? 'required' : 'nullable', 'string', 'min:4', 'same:form.password_confirmation'],
            'form.password_confirmation' => [$hasAccountCreation ? 'required' : 'nullable', 'string', 'min:4'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.product_url' => ['nullable', 'url:http,https', 'max:2048'],
            'items.*.description' => ['nullable', 'string', 'max:2000'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1', 'max:999'],
            'boxes_small' => ['nullable', 'integer', 'min:0', 'max:99'],
            'boxes_medium' => ['nullable', 'integer', 'min:0', 'max:99'],
            'boxes_large' => ['nullable', 'integer', 'min:0', 'max:99'],
        ];
    }

    protected function validationAttributes(): array
    {
        $attributes = [
            'form.name' => __('name'),
            'form.email' => __('email'),
            'form.whatsapp' => __('phone or WhatsApp'),
            'form.password' => __('password'),
            'form.password_confirmation' => __('confirm password'),
        ];

        foreach ($this->items as $index => $item) {
            $num = $index + 1;
            $attributes["items.{$index}.product_name"] = __('product')." #{$num}";
            $attributes["items.{$index}.product_url"] = __('product link')." #{$num}";
            $attributes["items.{$index}.description"] = __('message / notes')." #{$num}";
            $attributes["items.{$index}.quantity"] = __('quantity')." #{$num}";
        }

        return $attributes;
    }

    // Support legacy form.product_name, form.product_url, form.description for backwards compatibility & tests
    public function setLegacyProduct(string $name, ?string $url = null, ?string $description = null): void
    {
        $this->items[0]['product_name'] = $name;
        $this->items[0]['product_url'] = (string) $url;
        $this->items[0]['description'] = (string) $description;
    }

    public function updated($propertyName): void
    {
        // Bridge form.product_* to items[0] if set directly
        if ($propertyName === 'form.product_name') {
            $this->items[0]['product_name'] = $this->form['product_name'] ?? '';
        } elseif ($propertyName === 'form.product_url') {
            $this->items[0]['product_url'] = $this->form['product_url'] ?? '';
        } elseif ($propertyName === 'form.description') {
            $this->items[0]['description'] = $this->form['description'] ?? '';
        }
    }

    public function submit(): void
    {
        // Sync legacy keys if they were set directly in form
        if (! empty($this->form['product_name']) && empty($this->items[0]['product_name'])) {
            $this->items[0]['product_name'] = $this->form['product_name'];
            $this->items[0]['product_url'] = $this->form['product_url'] ?? '';
            $this->items[0]['description'] = $this->form['description'] ?? '';
        }

        $validated = $this->validate();

        $customer = Customer::firstOrCreate(
            ['email' => $validated['form']['email']],
            [
                'name' => $validated['form']['name'],
                'whatsapp' => $validated['form']['whatsapp'] ?? null,
                'registered_at' => today(),
            ]
        );

        // Create User Account if requested
        if (! empty($validated['form']['create_account']) && ! empty($validated['form']['password'])) {
            $user = User::where('email', $validated['form']['email'])->first();
            if (! $user) {
                $user = User::create([
                    'name' => $validated['form']['name'],
                    'email' => $validated['form']['email'],
                    'password' => Hash::make($validated['form']['password']),
                    'phone' => $validated['form']['whatsapp'] ?? null,
                    'whatsapp' => $validated['form']['whatsapp'] ?? null,
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

        $rates = $this->rates;
        $smallRate = (float) ($rates['box_small_heavy_duty'] ?? 15.0);
        $mediumRate = (float) ($rates['box_medium_heavy_duty'] ?? 20.0);
        $largeRate = (float) ($rates['box_large_heavy_duty'] ?? 25.0);

        $services = ['personal_shopper'];
        if ($this->boxes_small > 0 || $this->boxes_medium > 0 || $this->boxes_large > 0) {
            $services[] = 'packing';
        }

        $created = 0;
        foreach ($validated['items'] as $index => $item) {
            $req = PurchaseRequest::create([
                'customer_id' => $customer->id,
                'product_name' => $item['product_name'],
                'product_url' => ! empty($item['product_url']) ? $item['product_url'] : null,
                'description' => ! empty($item['description']) ? $item['description'] : null,
                'quantity' => ! empty($item['quantity']) ? (int) $item['quantity'] : 1,
                'services' => $services,
            ]);

            // Add packing cost items to the first request if multiple items
            if ($index === 0) {
                if ($this->boxes_small > 0) {
                    CostItem::create([
                        'costable_type' => PurchaseRequest::class,
                        'costable_id' => $req->id,
                        'type' => CostType::PackingFee,
                        'description' => "Caja Small Heavy Duty ({$this->boxes_small} x $".number_format($smallRate, 2).')',
                        'amount' => $this->boxes_small * $smallRate,
                    ]);
                }
                if ($this->boxes_medium > 0) {
                    CostItem::create([
                        'costable_type' => PurchaseRequest::class,
                        'costable_id' => $req->id,
                        'type' => CostType::PackingFee,
                        'description' => "Caja Mediana Heavy Duty ({$this->boxes_medium} x $".number_format($mediumRate, 2).')',
                        'amount' => $this->boxes_medium * $mediumRate,
                    ]);
                }
                if ($this->boxes_large > 0) {
                    CostItem::create([
                        'costable_type' => PurchaseRequest::class,
                        'costable_id' => $req->id,
                        'type' => CostType::PackingFee,
                        'description' => "Caja Larga Heavy Duty ({$this->boxes_large} x $".number_format($largeRate, 2).')',
                        'amount' => $this->boxes_large * $largeRate,
                    ]);
                }
            }

            AdminNotifier::notifyNewPurchaseRequest($req);
            $created++;
        }

        $this->createdCount = $created;
        $this->reset('form');
        $this->boxes_small = 0;
        $this->boxes_medium = 0;
        $this->boxes_large = 0;
        $this->items = [$this->emptyItem()];
        $this->sent = true;
    }

    public function resetForm(): void
    {
        $this->sent = false;
        $this->createdCount = 0;
        $this->boxes_small = 0;
        $this->boxes_medium = 0;
        $this->boxes_large = 0;
        $this->items = [$this->emptyItem()];
    }

    public function render()
    {
        return view('livewire.public-request-form');
    }
}
