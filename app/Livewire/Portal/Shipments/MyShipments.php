<?php

namespace App\Livewire\Portal\Shipments;

use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.portal')]
#[Title('My Shipments')]
class MyShipments extends Component
{
    use WithPagination;

    public function render()
    {
        $customer = auth()->user()->customer;

        $shipments = $customer
            ? $customer->shipments()->withCount('packages')->latest()->paginate(10)
            : new LengthAwarePaginator([], 0, 10);

        return view('livewire.portal.shipments.my-shipments', [
            'shipments' => $shipments,
        ]);
    }
}
