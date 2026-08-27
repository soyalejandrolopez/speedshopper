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
        'product_name' => '',
        'product_url' => '',
        'description' => '',
    ];

    public bool $sent = false;

    protected function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.email' => ['required', 'email', 'max:255'],
            'form.whatsapp' => ['nullable', 'string', 'max:50'],
            'form.product_name' => ['required', 'string', 'max:255'],
            'form.product_url' => ['nullable', 'url:http,https', 'max:2048'],
            'form.description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function submit(): void
    {
        $validated = $this->validate()['form'];

        $customer = Customer::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'whatsapp' => $validated['whatsapp'],
                'registered_at' => today(),
            ]
        );

        PurchaseRequest::create([
            'customer_id' => $customer->id,
            'product_name' => $validated['product_name'],
            'product_url' => $validated['product_url'],
            'description' => $validated['description'],
        ]);

        $this->reset('form');
        $this->sent = true;
    }

    public function render()
    {
        return view('livewire.public-request-form');
    }
}
