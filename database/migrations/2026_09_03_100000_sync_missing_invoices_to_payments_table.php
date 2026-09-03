<?php

use App\Enums\RequestStatus;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Find all non-cancelled purchase requests that don't have a payment record
        $requests = PurchaseRequest::where('status', '!=', RequestStatus::Cancelled->value)
            ->whereDoesntHave('payments')
            ->get();

        foreach ($requests as $req) {
            $cost = (float) $req->total_cost;
            if ($cost == 0.0 && $req->unit_price) {
                $cost = (float) $req->unit_price * max(1, $req->quantity);
            }

            $isPurchased = in_array($req->status?->value ?? $req->status, [
                RequestStatus::Purchased->value,
                RequestStatus::InTransit->value,
                RequestStatus::Received->value,
                RequestStatus::Packing->value,
                RequestStatus::Ready->value,
                RequestStatus::Shipped->value,
                RequestStatus::Delivered->value,
            ], true);

            Payment::create([
                'customer_id' => $req->customer_id,
                'billable_type' => PurchaseRequest::class,
                'billable_id' => $req->id,
                'invoice_total' => $cost,
                'amount_paid' => $isPurchased ? $cost : 0.0,
                'payment_method' => null,
                'reference' => $req->number,
                'paid_at' => $isPurchased ? ($req->created_at ?? now()) : null,
                'notes' => 'Factura sincronizada '.$req->number,
                'created_at' => $req->created_at ?? now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
