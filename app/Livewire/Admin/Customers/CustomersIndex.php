<?php

namespace App\Livewire\Admin\Customers;

use App\Concerns\SwalNotifies;
use App\Concerns\ValidatesWithFormRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Customers')]
class CustomersIndex extends Component
{
    use SwalNotifies, ValidatesWithFormRequest, WithPagination;

    public string $search = '';

    public bool $showForm = false;

    public ?int $editingId = null;

    public array $form = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'whatsapp' => '',
        'address' => '',
        'city' => '',
        'country' => '',
        'notes' => '',
        'registered_at' => null,
    ];

    protected function rules(): array
    {
        return $this->rulesFrom(new StoreCustomerRequest, []);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->reset('form', 'editingId');
        $this->form['registered_at'] = today()->toDateString();
        $this->showForm = true;
    }

    public function edit(Customer $customer): void
    {
        $this->resetValidation();
        $this->editingId = $customer->id;
        $this->form = $customer->only([
            'name', 'email', 'phone', 'whatsapp', 'address', 'city', 'country', 'notes',
        ]);
        $this->form['registered_at'] = $customer->registered_at?->toDateString();
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validatedData();

        if ($this->editingId) {
            $customer = Customer::findOrFail($this->editingId);
            $this->authorize('update', $customer);
            $customer->update($data);
            $this->swalUpdated();
        } else {
            $this->authorize('create', Customer::class);
            $customer = Customer::create($data);

            if ($customer->email) {
                $user = User::firstWhere('email', $customer->email);
                if ($user && ! $user->hasAnyRole(['admin', 'client'])) {
                    $user->assignRole('client');
                    $customer->update(['user_id' => $user->id]);
                }
            }

            $this->swalSaved();
        }

        $this->showForm = false;
    }

    public function delete(Customer $customer): void
    {
        $this->authorize('delete', $customer);
        $customer->delete();
        $this->swalDeleted();
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetValidation();
    }

    public function render()
    {
        $customers = Customer::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('number', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('whatsapp', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.customers.customers-index', [
            'customers' => $customers,
        ]);
    }
}
