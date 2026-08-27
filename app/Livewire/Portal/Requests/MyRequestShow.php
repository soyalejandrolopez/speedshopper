<?php

namespace App\Livewire\Portal\Requests;

use App\Models\PurchaseRequest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Request Details')]
class MyRequestShow extends Component
{
    public PurchaseRequest $purchaseRequest;

    public function mount(PurchaseRequest $purchaseRequest): void
    {
        abort_unless($purchaseRequest->customer_id === auth()->user()->customer?->id, 403, __('This record does not belong to your account.'));
    }

    public function render()
    {
        return view('livewire.portal.requests.my-request-show');
    }
}
