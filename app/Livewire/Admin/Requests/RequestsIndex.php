<?php

namespace App\Livewire\Admin\Requests;

use App\Concerns\ValidatesWithFormRequest;
use App\Enums\RequestStatus;
use App\Http\Requests\StorePurchaseRequestRequest;
use App\Models\Customer;
use App\Models\PurchaseRequest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Purchase Requests')]
class RequestsIndex extends Component
{
    use ValidatesWithFormRequest, WithPagination;

    public string $search = '';

    public string $status = 'all';

    #[Url]
    public ?int $customer = null;

    public bool $showForm = false;

    public ?int $editingId = null;

    public array $form = [
        'customer_id' => null,
        'product_name' => '',
        'product_url' => '',
        'store' => '',
        'description' => '',
        'size_color' => '',
        'quantity' => 1,
        'unit_price' => null,
        'discount_found' => null,
        'notes' => '',
    ];

    protected function rules(): array
    {
        return $this->rulesFrom(new StorePurchaseRequestRequest, []);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->reset('form', 'editingId');
        $this->form['quantity'] = 1;
        if ($this->customer) {
            $this->form['customer_id'] = $this->customer;
        }
        $this->showForm = true;
    }

    public function edit(PurchaseRequest $purchaseRequest): void
    {
        $this->resetValidation();
        $this->editingId = $purchaseRequest->id;
        $this->form = $purchaseRequest->only([
            'customer_id', 'product_name', 'product_url', 'store', 'description',
            'size_color', 'quantity', 'unit_price', 'discount_found', 'notes',
        ]);
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetValidation();
    }

    public function save(): void
    {
        $data = $this->validatedData();

        if ($this->editingId) {
            $purchaseRequest = PurchaseRequest::findOrFail($this->editingId);
            $this->authorize('update', $purchaseRequest);
            $purchaseRequest->update($data);
            session()->flash('success', __('Purchase Request updated successfully.'));
        } else {
            $this->authorize('create', PurchaseRequest::class);
            PurchaseRequest::create($data);
            session()->flash('success', __('Purchase Request created successfully.'));
        }

        $this->showForm = false;
    }

    public function delete(PurchaseRequest $purchaseRequest): void
    {
        $this->authorize('delete', $purchaseRequest);
        $purchaseRequest->delete();
        session()->flash('success', __('Purchase Request deleted.'));
    }

    public function render()
    {
        $requests = PurchaseRequest::query()
            ->with('customer')
            ->when($this->customer, fn ($q) => $q->where('customer_id', $this->customer))
            ->when($this->status !== 'all', fn ($q) => $q->where('status', $this->status))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('number', 'like', "%{$this->search}%")
                        ->orWhere('product_name', 'like', "%{$this->search}%")
                        ->orWhere('store', 'like', "%{$this->search}%")
                        ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.requests.requests-index', [
            'requests' => $requests,
            'statuses' => RequestStatus::cases(),
            'customers' => Customer::orderBy('name')->get(),
        ]);
    }
}
