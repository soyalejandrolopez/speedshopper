<?php

namespace App\Livewire\Admin\Payments;

use App\Models\Payment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Payment Details')]
class PaymentShow extends Component
{
    public Payment $payment;

    public function mount(Payment $payment): void
    {
        $this->authorize('view', $payment);

        $this->payment->load(['customer', 'billable']);
    }

    public function render()
    {
        return view('livewire.admin.payments.payment-show');
    }
}
