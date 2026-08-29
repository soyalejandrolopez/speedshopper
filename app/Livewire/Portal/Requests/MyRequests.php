<?php

namespace App\Livewire\Portal\Requests;

use App\Concerns\SwalNotifies;
use App\Concerns\ValidatesWithFormRequest;
use App\Http\Requests\StorePurchaseRequestRequest;
use App\Models\PurchaseRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.portal')]
#[Title('My Requests')]
class MyRequests extends Component
{
    use SwalNotifies, ValidatesWithFormRequest, WithPagination;

    public bool $showForm = false;

    public array $form = [
        'product_name' => '',
        'product_url' => '',
        'store' => '',
        'description' => '',
        'size_color' => '',
        'quantity' => 1,
        'unit_price' => null,
        'notes' => '',
    ];

    protected function rules(): array
    {
        $rules = $this->rulesFrom(new StorePurchaseRequestRequest);
        unset($rules['form.customer_id']);

        return $rules;
    }

    public function openForm(): void
    {
        $this->resetValidation();
        $this->reset('form');
        $this->form['quantity'] = 1;
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->authorize('create', PurchaseRequest::class);

        $data = $this->validatedData();
        $data['customer_id'] = auth()->user()->customer?->id;

        if (! $data['customer_id']) {
            $this->swalError();

            return;
        }

        PurchaseRequest::create($data);
        $this->showForm = false;
        $this->swalSaved();
    }

    public function render()
    {
        $customer = auth()->user()->customer;

        $requests = $customer
            ? $customer->purchaseRequests()->latest()->paginate(10)
            : new LengthAwarePaginator([], 0, 10);

        return view('livewire.portal.requests.my-requests', [
            'requests' => $requests,
        ]);
    }
}
