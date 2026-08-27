<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShipmentRequest;
use App\Http\Requests\UpdateShipmentRequest;
use App\Http\Resources\ShipmentResource;
use App\Models\Shipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShipmentController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Shipment::class);

        $user = request()->user();

        $shipments = Shipment::query()
            ->with(['customer', 'packages', 'costItems'])
            ->when($user->isClient(), fn ($q) => $q->where('customer_id', $user->customer?->id))
            ->latest()
            ->paginate(15);

        return ShipmentResource::collection($shipments);
    }

    public function store(StoreShipmentRequest $request): ShipmentResource
    {
        $data = $request->validated();
        $packageIds = $data['package_ids'] ?? [];
        unset($data['package_ids']);

        $shipment = Shipment::create($data);
        $shipment->packages()->sync($packageIds);

        return new ShipmentResource($shipment->load(['customer', 'packages']));
    }

    public function show(Shipment $shipment): ShipmentResource
    {
        $this->authorize('view', $shipment);

        return new ShipmentResource(
            $shipment->load(['customer', 'packages', 'costItems', 'statusHistory.user'])
        );
    }

    public function update(UpdateShipmentRequest $request, Shipment $shipment): ShipmentResource
    {
        $this->authorize('update', $shipment);

        $data = $request->validated();
        $packageIds = $data['package_ids'] ?? [];
        unset($data['package_ids']);

        $shipment->update($data);
        $shipment->packages()->sync($packageIds);

        return new ShipmentResource($shipment->load(['customer', 'packages']));
    }

    public function destroy(Shipment $shipment): JsonResponse
    {
        $this->authorize('delete', $shipment);
        $shipment->delete();

        return response()->json(['message' => 'OK']);
    }
}
