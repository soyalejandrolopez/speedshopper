<?php

namespace App\Livewire\Admin\Payments;

use App\Concerns\SwalNotifies;
use App\Concerns\ValidatesWithFormRequest;
use App\Enums\PaymentMethod;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Shipment;
use App\Services\FinanceService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Payments')]
class PaymentsIndex extends Component
{
    use SwalNotifies, ValidatesWithFormRequest, WithPagination;

    public string $search = '';

    public bool $showForm = false;

    public ?int $editingId = null;

    public ?float $pendingBalance = null;

    public array $form = [
        'customer_id' => null,
        'customer_search' => '',
        'billable_type' => '',
        'billable_id' => null,
        'reference' => '',
        'invoice_total' => null,
        'amount_paid' => null,
        'payment_method' => '',
        'paid_at' => null,
        'notes' => '',
    ];

    protected function rules(): array
    {
        $rules = $this->rulesFrom(new StorePaymentRequest, []);

        if (! $this->editingId && $this->pendingBalance !== null) {
            $rules['form.amount_paid'] = array_merge($rules['form.amount_paid'], ['max:' . $this->pendingBalance]);
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'form.amount_paid.max' => __('El monto pagado no puede exceder el balance pendiente (:max).', ['max' => money($this->pendingBalance ?? 0)]),
            'form.amount_paid.lte' => __('El monto pagado no puede ser mayor al total de la factura.'),
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFormCustomerId(): void
    {
        $this->form['billable_type'] = '';
        $this->form['billable_id'] = null;
        $this->pendingBalance = null;

        // Check if the customer has an outstanding balance.
        if ($this->form['customer_id'] && ! $this->editingId) {
            $customer = Customer::find($this->form['customer_id']);

            if ($customer && $customer->balance_due > 0) {
                $this->pendingBalance = (float) $customer->balance_due;
                $this->form['invoice_total'] = number_format($customer->balance_due, 2, '.', '');
            } else {
                $this->form['invoice_total'] = '';
            }
        }
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->reset('form', 'editingId', 'pendingBalance');
        $this->form['paid_at'] = now()->toDateString();
        $this->showForm = true;
    }

    public function edit(Payment $payment): void
    {
        $this->resetValidation();
        $this->pendingBalance = null;
        $this->editingId = $payment->id;
        $this->form = $payment->only([
            'customer_id', 'invoice_total', 'amount_paid', 'notes', 'reference',
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
        $this->form['customer_id'] = $customerId;
        $this->form['customer_search'] = $name;
        $this->updatedFormCustomerId();
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetValidation();
    }

    public function save(): void
    {
        // If customer has a pending balance and invoice_total is empty, enforce it
        if (! $this->editingId && ! empty($this->form['customer_id']) && empty($this->form['invoice_total'])) {
            $customer = Customer::find($this->form['customer_id']);
            if ($customer && $customer->balance_due > 0) {
                $this->pendingBalance = (float) $customer->balance_due;
                $this->form['invoice_total'] = number_format($customer->balance_due, 2, '.', '');
            }
        }

        $data = $this->validatedData();

        // Si estamos abonando a un balance pendiente, no es una nueva factura.
        if (! $this->editingId && $this->pendingBalance !== null && empty($data['billable_type'])) {
            $data['invoice_total'] = 0;
        }

        // Campos opcionales: vacío → null/0 (evita error de cast a enum, morphTo o NOT NULL).
        $data['payment_method'] = $data['payment_method'] ?: null;
        $data['amount_paid'] = $data['amount_paid'] ?: 0;
        $data['reference'] = $data['reference'] ?: null;
        if (empty($data['billable_type'])) {
            $data['billable_type'] = null;
            $data['billable_id'] = null;
        }

        // Convert short aliases to fully-qualified morph class names.
        if ($data['billable_type'] && in_array($data['billable_type'], ['purchase_request', 'shipment'])) {
            $data['billable_type'] = match ($data['billable_type']) {
                'shipment' => Shipment::class,
                default => PurchaseRequest::class,
            };
        }

        if ($this->editingId) {
            $payment = Payment::findOrFail($this->editingId);
            $this->authorize('update', $payment);
            $payment->update($data);
            $this->swalUpdated();
        } else {
            $this->authorize('create', Payment::class);
            Payment::create($data);
            $this->swalSaved();
        }

        $this->showForm = false;
    }

    public function delete(Payment $payment): void
    {
        $this->authorize('delete', $payment);
        $payment->delete();
        $this->swalDeleted();
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

        $financeMetrics = app(FinanceService::class)->getMetrics();
        $totalInvoiced = $financeMetrics['total_invoiced'];
        $totalCollected = $financeMetrics['total_collected'];
        $totalBalanceDue = $financeMetrics['total_balance_due'];

        return view('livewire.admin.payments.payments-index', [
            'payments' => $payments,
            'customers' => Customer::orderBy('name')->get(),
            'methods' => PaymentMethod::cases(),
            'totalTransactions' => Payment::count(),
            'totalCollected' => $totalCollected,
            'totalInvoiced' => $totalInvoiced,
            'totalBalanceDue' => $totalBalanceDue,
        ]);
    }
}
