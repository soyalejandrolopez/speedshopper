<?php

namespace App\Livewire\Admin\Customers;

use App\Models\Customer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Customer Details')]
class CustomerShow extends Component
{
    public Customer $customer;

    public function mount(Customer $customer): void
    {
        $this->authorize('view', $customer);
    }

    public function render()
    {
        return view('livewire.admin.customers.customer-show', [
            'requests' => $this->customer->purchaseRequests()->latest()->limit(10)->get(),
            'packages' => $this->customer->packages()->latest()->limit(10)->get(),
            'shipments' => $this->customer->shipments()->latest()->limit(10)->get(),
            'payments' => $this->customer->payments()->latest()->limit(10)->get(),
        ]);
    }
}
