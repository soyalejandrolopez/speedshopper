<?php

namespace App\Livewire\Admin\Shipments;

use App\Enums\CostType;
use App\Models\Shipment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Shipment Details')]
class ShipmentShow extends Component
{
    public Shipment $shipment;

    public string $newStatus = '';

    public string $transitionNote = '';

    public array $costForm = [
        'type' => '',
        'description' => '',
        'amount' => '',
    ];

    public function mount(Shipment $shipment): void
    {
        $this->authorize('view', $shipment);
        $this->costForm['type'] = CostType::InternationalShipping->value;
    }

    public function transitionStatus(): void
    {
        $this->authorize('update', $this->shipment);

        $validated = $this->validate([
            'newStatus' => ['required', 'string', 'in:'.implode(',', $this->shipment->status->nextStatuses())],
            'transitionNote' => ['nullable', 'string', 'max:500'],
        ]);

        $this->shipment->transitionTo(
            $validated['newStatus'],
            $validated['transitionNote'],
            auth()->user()
        );

        if ($validated['newStatus'] === 'in_transit') {
            $this->shipment->packages()->update(['status' => 'shipped']);
        }
        if ($validated['newStatus'] === 'delivered') {
            $this->shipment->packages()->update(['status' => 'delivered']);
        }

        $this->reset('newStatus', 'transitionNote');
        session()->flash('success', __('Status updated successfully.'));
    }

    public function addCost(): void
    {
        $this->authorize('update', $this->shipment);

        $validated = $this->validate([
            'costForm.type' => ['required', 'string'],
            'costForm.description' => ['nullable', 'string', 'max:255'],
            'costForm.amount' => ['required', 'numeric', 'min:0'],
        ]);

        $this->shipment->costItems()->create([
            'type' => CostType::from($validated['costForm']['type']),
            'description' => $validated['costForm']['description'],
            'amount' => $validated['costForm']['amount'],
        ]);

        $this->reset('costForm');
        $this->costForm['type'] = CostType::InternationalShipping->value;
        session()->flash('success', __('Cost added successfully.'));
    }

    public function removeCost(int $costItemId): void
    {
        $this->authorize('update', $this->shipment);
        $this->shipment->costItems()->where('id', $costItemId)->delete();
        session()->flash('success', __('Deleted.'));
    }

    public function render()
    {
        return view('livewire.admin.shipments.shipment-show', [
            'costTypes' => CostType::forShipments(),
        ]);
    }
}
