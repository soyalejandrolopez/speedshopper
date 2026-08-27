<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\PurchaseRequest;
use Livewire\Component;

class PublicRequestForm extends Component
{
    public array $form = [
        'name' => '',
        'email' => '',
        'whatsapp' => '',
    ];

    /** @var array<int, array{product_name: string, product_url: string, description: string, quantity: int}> */
    public array $items = [];

    public bool $sent = false;

    public int $createdCount = 0;

    public function mount(): void
    {
        $this->items = [
            $this->emptyItem(),
        ];
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
        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.email' => ['required', 'email', 'max:255'],
            'form.whatsapp' => ['nullable', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.product_url' => ['nullable', 'url:http,https', 'max:2048'],
            'items.*.description' => ['nullable', 'string', 'max:2000'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1', 'max:999'],
        ];
    }

    protected function validationAttributes(): array
    {
        $attributes = [
            'form.name' => __('name'),
            'form.email' => __('email'),
            'form.whatsapp' => __('phone or WhatsApp'),
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

        $created = 0;
        foreach ($validated['items'] as $item) {
            PurchaseRequest::create([
                'customer_id' => $customer->id,
                'product_name' => $item['product_name'],
                'product_url' => ! empty($item['product_url']) ? $item['product_url'] : null,
                'description' => ! empty($item['description']) ? $item['description'] : null,
                'quantity' => ! empty($item['quantity']) ? (int) $item['quantity'] : 1,
            ]);
            $created++;
        }

        $this->createdCount = $created;
        $this->reset('form');
        $this->items = [$this->emptyItem()];
        $this->sent = true;
    }

    public function resetForm(): void
    {
        $this->sent = false;
        $this->createdCount = 0;
        $this->items = [$this->emptyItem()];
    }

    public function render()
    {
        return view('livewire.public-request-form');
    }
}
