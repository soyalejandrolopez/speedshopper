<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Payment::class);

        $user = request()->user();

        $payments = Payment::query()
            ->with('customer')
            ->when($user->isClient(), fn ($q) => $q->where('customer_id', $user->customer?->id))
            ->latest()
            ->paginate(15);

        return PaymentResource::collection($payments);
    }

    public function store(StorePaymentRequest $request): PaymentResource
    {
        return new PaymentResource(Payment::create($request->validated())->load('customer'));
    }

    public function show(Payment $payment): PaymentResource
    {
        $this->authorize('view', $payment);

        return new PaymentResource($payment->load('customer'));
    }

    public function update(UpdatePaymentRequest $request, Payment $payment): PaymentResource
    {
        $this->authorize('update', $payment);
        $payment->update($request->validated());

        return new PaymentResource($payment->load('customer'));
    }

    public function destroy(Payment $payment): JsonResponse
    {
        $this->authorize('delete', $payment);
        $payment->delete();

        return response()->json(['message' => 'OK']);
    }
}
