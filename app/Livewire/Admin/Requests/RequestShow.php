<?php

namespace App\Livewire\Admin\Requests;

use App\Concerns\SwalNotifies;
use App\Enums\CostType;
use App\Enums\PackageStatus;
use App\Enums\ShipmentStatus;
use App\Models\PurchaseRequest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Request Details')]
class RequestShow extends Component
{
    use SwalNotifies;

    public PurchaseRequest $purchaseRequest;

    public string $newStatus = '';

    public string $transitionNote = '';

    public array $costForm = [
        'type' => '',
        'description' => '',
        'amount' => '',
    ];

    public function mount(PurchaseRequest $purchaseRequest): void
    {
        $this->authorize('view', $purchaseRequest);
        $this->costForm['type'] = CostType::ProductCost->value;
    }

    public function transitionStatus(): void
    {
        $this->authorize('update', $this->purchaseRequest);

        $validated = $this->validate([
            'newStatus' => ['required', 'string', 'in:'.implode(',', $this->purchaseRequest->status->nextStatuses())],
            'transitionNote' => ['nullable', 'string', 'max:500'],
        ]);

        $this->purchaseRequest->transitionTo(
            $validated['newStatus'],
            $validated['transitionNote'],
            auth()->user()
        );

        $this->syncChildren($validated['newStatus']);

        $this->reset('newStatus', 'transitionNote');
        $this->swalUpdated();
    }

    /**
     * Keep packages and shipments in sync as the order moves through the unified flow.
     */
    protected function syncChildren(string $status): void
    {
        $order = match ($status) {
            'packing' => PackageStatus::Packing,
            'ready' => PackageStatus::Ready,
            'shipped' => PackageStatus::Shipped,
            'delivered' => PackageStatus::Delivered,
            default => null,
        };

        $packages = $this->purchaseRequest->packages()->get();

        if ($order) {
            $rank = [
                PackageStatus::Received->value => 0,
                PackageStatus::Storing->value => 1,
                PackageStatus::Packing->value => 2,
                PackageStatus::Ready->value => 3,
                PackageStatus::Shipped->value => 4,
                PackageStatus::Delivered->value => 5,
            ];

            foreach ($packages as $package) {
                if (($rank[$package->status->value] ?? 99) < $rank[$order->value]) {
                    $package->update(['status' => $order->value]);
                }
            }
        }

        if ($status === 'shipped') {
            foreach ($packages->flatMap(fn ($p) => $p->shipments)->unique('id') as $shipment) {
                if ($shipment->status === ShipmentStatus::Ready) {
                    $shipment->update(['status' => ShipmentStatus::InTransit->value, 'shipped_at' => today()]);
                }
            }
        }

        if ($status === 'delivered') {
            foreach ($packages->flatMap(fn ($p) => $p->shipments)->unique('id') as $shipment) {
                $shipment->update(['status' => ShipmentStatus::Delivered->value, 'delivered_at' => today()]);
            }
        }
    }

    public function addCost(): void
    {
        $this->authorize('update', $this->purchaseRequest);

        $validated = $this->validate([
            'costForm.type' => ['required', 'string'],
            'costForm.description' => ['nullable', 'string', 'max:255'],
            'costForm.amount' => ['required', 'numeric', 'min:0'],
        ]);

        $this->purchaseRequest->costItems()->create([
            'type' => CostType::from($validated['costForm']['type']),
            'description' => $validated['costForm']['description'],
            'amount' => $validated['costForm']['amount'],
        ]);

        $this->reset('costForm');
        $this->costForm['type'] = CostType::ProductCost->value;
        $this->swalSaved();
    }

    public function removeCost(int $costItemId): void
    {
        $this->authorize('update', $this->purchaseRequest);
        $this->purchaseRequest->costItems()->where('id', $costItemId)->delete();
        $this->swalDeleted();
    }

    public function render()
    {
        return view('livewire.admin.requests.request-show', [
            'costTypes' => CostType::forRequests(),
        ]);
    }
}
