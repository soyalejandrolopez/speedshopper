<?php

namespace App\Livewire\Admin\Payments;

use App\Concerns\ValidatesWithFormRequest;
use App\Enums\PaymentMethod;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Shipment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Payments')]
class PaymentsIndex extends Component
{
    use ValidatesWithFormRequest, WithPagination;

    public string $search = '';

    public bool $showForm = false;

    public ?int $editingId = null;

    public array $form = [
        'customer_id' => null,
        'customer_search' => '',
        'billable_type' => '',
        'billable_id' => null,
        'invoice_total' => null,
        'amount_paid' => null,
        'payment_method' => '',
        'paid_at' => null,
        'notes' => '',
    ];

    protected function rules(): array
    {
        return $this->rulesFrom(new StorePaymentRequest, []);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFormCustomerId(): void
    {
        $this->form['billable_type'] = '';
        $this->form['billable_id'] = null;
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->reset('form', 'editingId');
        $this->form['paid_at'] = now()->toDateString();
        $this->showForm = true;
    }

    public function edit(Payment $payment): void
    {
        $this->resetValidation();
        $this->editingId = $payment->id;
        $this->form = $payment->only([
            'customer_id', 'invoice_total', 'amount_paid', 'notes',
        ]);
        $this->form['customer_search'] = $payment->customer?->name ?? '';
        $this->form['billable_type'] = $payment->billable_type ? class_basename($payment->billable_type) : '';
        $this->form['billable_id'] = $payment->billable_id;
        $this->form['payment_method'] = $payment->payment_method?->value ?? '';
        $this->form['paid_at'] = $payment->paid_at?->toDateString();
        $this->showForm = true;
    }

    public function selectCustomer(?int $customerId, string $name): void
    {
        $this->form["customer_id"] = $customerId;
        $this->form["customer_search"] = $name;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetValidation();
    }

    public function save(): void
    {
        $data = $this->validatedData();

        // Campos opcionales: vacío → null (evita error de cast a enum / morphTo).
        $data['payment_method'] = $data['payment_method'] ?: null;
        if (empty($data['billable_type'])) {
            $data['billable_type'] = null;
            $data['billable_id'] = null;
        }

        if ($data['billable_type']) {
            $data['billable_type'] = match ($data['billable_type']) {
                'shipment' => Shipment::class,
                default => PurchaseRequest::class,
            };
        }

        if ($this->editingId) {
            $payment = Payment::findOrFail($this->editingId);
            $this->authorize('update', $payment);
            $payment->update($data);
            session()->flash('success', __('Payment updated successfully.'));
        } else {
            $this->authorize('create', Payment::class);
            Payment::create($data);
            session()->flash('success', __('Payment created successfully.'));
        }

        $this->showForm = false;
    }

    public function delete(Payment $payment): void
    {
        $this->authorize('delete', $payment);
        $payment->delete();
        session()->flash('success', __('Payment deleted.'));
    }

    public function render()
    {
        $payments = Payment::query()
            ->with('customer')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('number', 'like', "%{$this->search}%")
                        ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.payments.payments-index', [
            'payments' => $payments,
            'customers' => Customer::orderBy('name')->get(),
            'methods' => PaymentMethod::cases(),
        ]);
    }
}
