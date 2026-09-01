<?php

namespace App\Livewire\Admin\Shipments;

use App\Concerns\SwalNotifies;
use App\Concerns\ValidatesWithFormRequest;
use App\Enums\ShipmentStatus;
use App\Http\Requests\StoreShipmentRequest;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Shipment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Shipments')]
class ShipmentsIndex extends Component
{
    use SwalNotifies, ValidatesWithFormRequest, WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = 'all';

    public bool $showForm = false;

    public ?int $editingId = null;

    public array $form = [
        'customer_id' => null,
        'customer_search' => '',
        'package_ids' => [],
        'carrier' => '',
        'destination_country' => '',
        'final_weight_lb' => null,
        'dimensions' => '',
        'international_tracking' => '',
        'shipping_cost' => null,
        'shipped_at' => null,
        'delivered_at' => null,
        'notes' => '',
    ];

    protected function rules(): array
    {
        return $this->rulesFrom(new StoreShipmentRequest, []);
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

    public function updatedFormCustomerId(): void
    {
        $this->form['package_ids'] = [];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->reset('form', 'editingId');
        $this->showForm = true;
    }

    public function edit(Shipment $shipment): void
    {
        $this->resetValidation();
        $this->editingId = $shipment->id;
        $this->form = $shipment->only([
            'customer_id', 'carrier', 'destination_country', 'final_weight_lb', 'dimensions',
            'international_tracking', 'shipping_cost', 'notes',
        ]);
        $this->form['customer_search'] = $shipment->customer?->name ?? '';
        $this->form['package_ids'] = $shipment->packages->pluck('id')->all();
        $this->form['shipped_at'] = $shipment->shipped_at?->toDateString();
        $this->form['delivered_at'] = $shipment->delivered_at?->toDateString();
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

        $packageIds = $data['package_ids'] ?? [];
        unset($data['package_ids']);

        if ($this->editingId) {
            $shipment = Shipment::findOrFail($this->editingId);
            $this->authorize('update', $shipment);
            $shipment->update($data);
            $shipment->packages()->sync($packageIds);
            $this->swalUpdated();
        } else {
            $this->authorize('create', Shipment::class);
            $shipment = Shipment::create($data);
            $shipment->packages()->sync($packageIds);

            if ($packageIds) {
                Package::whereIn('id', $packageIds)->update(['status' => 'ready']);
            }

            $this->swalSaved();
        }

        $this->showForm = false;
    }

    public function delete(Shipment $shipment): void
    {
        $this->authorize('delete', $shipment);
        $shipment->delete();
        $this->swalDeleted();
    }

    public function render()
    {
        $shipments = Shipment::query()
            ->with('customer')
            ->when($this->status !== 'all', fn ($q) => $q->where('status', $this->status))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('number', 'like', "%{$this->search}%")
                        ->orWhere('carrier', 'like', "%{$this->search}%")
                        ->orWhere('international_tracking', 'like', "%{$this->search}%")
                        ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->withCount('packages')
            ->latest()
            ->paginate(10);

        $availablePackages = collect();
        if (! empty($this->form['customer_id'])) {
            $availablePackages = Package::query()
                ->where('customer_id', $this->form['customer_id'])
                ->where('status', '!=', 'delivered')
                ->where(function ($q) {
                    $q->doesntHave('shipments')
                        ->orWhereHas('shipments', fn ($s) => $s->where('shipments.id', $this->editingId));
                })
                ->latest()
                ->get();
        }

        return view('livewire.admin.shipments.shipments-index', [
            'shipments' => $shipments,
            'statuses' => ShipmentStatus::cases(),
            'customers' => Customer::orderBy('name')->get(),
            'availablePackages' => $availablePackages,
            'totalCount' => Shipment::count(),
            'inTransitCount' => Shipment::where('status', ShipmentStatus::InTransit->value)->count(),
            'readyCount' => Shipment::where('status', ShipmentStatus::Ready->value)->count(),
            'deliveredCount' => Shipment::where('status', ShipmentStatus::Delivered->value)->count(),
        ]);
    }
}
