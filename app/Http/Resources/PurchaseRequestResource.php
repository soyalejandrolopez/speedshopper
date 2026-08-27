<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'product_name' => $this->product_name,
            'product_url' => $this->product_url,
            'store' => $this->store,
            'description' => $this->description,
            'size_color' => $this->size_color,
            'quantity' => $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'discount_found' => (float) $this->discount_found,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'total_cost' => $this->whenAppended('total_cost'),
            'notes' => $this->when($request->user()?->isAdmin(), $this->notes),
            'cost_items' => CostItemResource::collection($this->whenLoaded('costItems')),
            'status_history' => StatusHistoryResource::collection($this->whenLoaded('statusHistory')),
            'created_at' => $this->created_at,
        ];
    }
}
