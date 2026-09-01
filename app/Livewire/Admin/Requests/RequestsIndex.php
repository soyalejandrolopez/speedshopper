<?php

namespace App\Livewire\Admin\Requests;

use App\Concerns\SwalNotifies;
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
    use SwalNotifies, ValidatesWithFormRequest, WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = 'all';

    #[Url]
    public ?int $customer = null;

    public bool $showForm = false;

    public ?int $editingId = null;

    public array $form = [
        'customer_id' => null,
        'customer_search' => '',
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

    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->reset('form', 'editingId');
        $this->form['quantity'] = 1;
        if ($this->customer) {
            $this->form['customer_id'] = $this->customer;
            $this->form['customer_search'] = Customer::find($this->customer)?->name ?? '';
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
        $this->form['customer_search'] = $purchaseRequest->customer?->name ?? '';
        $this->showForm = true;
    }

    public function selectCustomer(?int $customerId, string $name): void
    {
        $this->form['customer_id'] = $customerId;
        $this->form['customer_search'] = $name;
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
            $this->swalUpdated();
        } else {
            $this->authorize('create', PurchaseRequest::class);
            PurchaseRequest::create($data);
            $this->swalSaved();
        }

        $this->showForm = false;
    }

    public function delete(PurchaseRequest $purchaseRequest): void
    {
        $this->authorize('delete', $purchaseRequest);
        $purchaseRequest->delete();
        $this->swalDeleted();
    }

    public function render()
    {
        $requests = PurchaseRequest::query()
            ->with('customer')
            ->when($this->customer, fn ($q) => $q->where('customer_id', $this->customer))
            ->when($this->status !== 'all', function ($q) {
                if ($this->status === 'open') {
                    $q->whereNotIn('status', [RequestStatus::Delivered->value, RequestStatus::Cancelled->value]);
                } else {
                    $q->where('status', $this->status);
                }
            })
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
            'totalCount' => PurchaseRequest::count(),
            'openCount' => PurchaseRequest::whereNotIn('status', [RequestStatus::Delivered->value, RequestStatus::Cancelled->value])->count(),
            'deliveredCount' => PurchaseRequest::where('status', RequestStatus::Delivered->value)->count(),
            'cancelledCount' => PurchaseRequest::where('status', RequestStatus::Cancelled->value)->count(),
        ]);
    }
}
