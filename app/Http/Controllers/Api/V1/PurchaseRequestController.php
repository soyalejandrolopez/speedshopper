<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseRequestRequest;
use App\Http\Requests\UpdatePurchaseRequestRequest;
use App\Http\Resources\PurchaseRequestResource;
use App\Models\PurchaseRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PurchaseRequestController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', PurchaseRequest::class);

        $user = request()->user();

        $requests = PurchaseRequest::query()
            ->with(['customer', 'costItems'])
            ->when($user->isClient(), fn ($q) => $q->where('customer_id', $user->customer?->id))
            ->latest()
            ->paginate(15);

        return PurchaseRequestResource::collection($requests);
    }

    public function store(StorePurchaseRequestRequest $request): PurchaseRequestResource
    {
        $data = $request->validated();

        if (! $request->user()->isAdmin()) {
            $data['customer_id'] = $request->user()->customer?->id;
            abort_if(! $data['customer_id'], 403);
        }

        $purchaseRequest = PurchaseRequest::create($data);

        return new PurchaseRequestResource($purchaseRequest->load('customer'));
    }

    public function show(PurchaseRequest $purchaseRequest): PurchaseRequestResource
    {
        $this->authorize('view', $purchaseRequest);

        return new PurchaseRequestResource(
            $purchaseRequest->load(['customer', 'costItems', 'statusHistory.user'])
        );
    }

    public function update(UpdatePurchaseRequestRequest $request, PurchaseRequest $purchaseRequest): PurchaseRequestResource
    {
        $this->authorize('update', $purchaseRequest);

        $purchaseRequest->update($request->validated());

        if ($request->has('status') && $purchaseRequest->wasChanged('status')) {
            $purchaseRequest->recordStatus($purchaseRequest->status->value, null, $request->user());
        }

        return new PurchaseRequestResource($purchaseRequest->load('customer'));
    }

    public function destroy(PurchaseRequest $purchaseRequest): JsonResponse
    {
        $this->authorize('delete', $purchaseRequest);
        $purchaseRequest->delete();

        return response()->json(['message' => 'OK']);
    }
}
