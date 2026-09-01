<?php

namespace App\Livewire\Admin\Packages;

use App\Concerns\SwalNotifies;
use App\Concerns\ValidatesWithFormRequest;
use App\Enums\PackageStatus;
use App\Http\Requests\StorePackageRequest;
use App\Models\Customer;
use App\Models\Package;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Packages')]
class PackagesIndex extends Component
{
    use SwalNotifies, ValidatesWithFormRequest, WithFileUploads, WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = 'all';

    #[Url]
    public string $filter = 'all';

    public bool $showForm = false;

    public ?int $editingId = null;

    public $photo;

    public array $form = [
        'customer_id' => null,
        'customer_search' => '',
        'purchase_request_id' => null,
        'store' => '',
        'original_tracking' => '',
        'received_at' => null,
        'weight_lb' => null,
        'location' => '',
        'notes' => '',
    ];

    protected function rules(): array
    {
        return $this->rulesFrom(new StorePackageRequest, ['photo']);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->filter = 'all';
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->status = 'all';
        $this->resetPage();
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->status = 'all';
        $this->resetPage();
    }

    public function updatedFormCustomerId(): void
    {
        $this->form['purchase_request_id'] = null;
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->reset('form', 'editingId', 'photo');
        $this->form['received_at'] = today()->toDateString();
        $this->showForm = true;
    }

    public function edit(Package $package): void
    {
        $this->resetValidation();
        $this->editingId = $package->id;
        $this->photo = null;
        $this->form = $package->only([
            'customer_id', 'purchase_request_id', 'store', 'original_tracking',
            'received_at', 'weight_lb', 'location', 'notes',
        ]);
        $this->form['customer_search'] = $package->customer?->name ?? '';
        $this->form['received_at'] = $package->received_at?->toDateString();
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

        if ($this->photo) {
            $data['photo_path'] = $this->photo->store('packages', 'public');
        }
        unset($data['photo']);

        if ($this->editingId) {
            $package = Package::findOrFail($this->editingId);
            $this->authorize('update', $package);
            $package->update($data);
            $this->swalUpdated();
        } else {
            $this->authorize('create', Package::class);
            Package::create($data);
            $this->swalSaved();
        }

        $this->photo = null;
        $this->showForm = false;
    }

    public function delete(Package $package): void
    {
        $this->authorize('delete', $package);
        $package->delete();
        $this->swalDeleted();
    }

    public function render()
    {
        $packages = Package::query()
            ->with('customer')
            ->when($this->filter === 'today', fn ($q) => $q->whereDate('received_at', today()))
            ->when($this->filter === 'stored', fn ($q) => $q->whereIn('status', ['received', 'storing', 'packing', 'ready']))
            ->when($this->filter === 'ready', fn ($q) => $q->where('status', 'ready'))
            ->when($this->status !== 'all', fn ($q) => $q->where('status', $this->status))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('number', 'like', "%{$this->search}%")
                        ->orWhere('store', 'like', "%{$this->search}%")
                        ->orWhere('original_tracking', 'like', "%{$this->search}%")
                        ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.packages.packages-index', [
            'packages' => $packages,
            'statuses' => PackageStatus::cases(),
            'customers' => Customer::orderBy('name')->get(),
            'totalCount' => Package::count(),
            'receivedTodayCount' => Package::whereDate('received_at', today())->count(),
            'storedCount' => Package::whereIn('status', ['received', 'storing', 'packing', 'ready'])->count(),
            'readyCount' => Package::where('status', 'ready')->count(),
        ]);
    }
}
