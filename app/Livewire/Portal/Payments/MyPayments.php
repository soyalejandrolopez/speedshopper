<?php

namespace App\Livewire\Portal\Payments;

use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.portal')]
#[Title('My Payments')]
class MyPayments extends Component
{
    use WithPagination;

    public function render()
    {
        $customer = auth()->user()->customer;

        $payments = $customer
            ? $customer->payments()->latest()->paginate(10)
            : new LengthAwarePaginator([], 0, 10);

        return view('livewire.portal.payments.my-payments', [
            'payments' => $payments,
            'balanceDue' => $customer?->balance_due ?? 0,
        ]);
    }
}
