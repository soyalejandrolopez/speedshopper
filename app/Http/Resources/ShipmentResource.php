<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'packages' => PackageResource::collection($this->whenLoaded('packages')),
            'carrier' => $this->carrier,
            'destination_country' => $this->destination_country,
            'final_weight_lb' => (float) $this->final_weight_lb,
            'dimensions' => $this->dimensions,
            'international_tracking' => $this->international_tracking,
            'shipping_cost' => (float) $this->shipping_cost,
            'shipped_at' => $this->shipped_at?->toDateString(),
            'delivered_at' => $this->delivered_at?->toDateString(),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'total_cost' => $this->whenAppended('total_cost'),
            'cost_items' => CostItemResource::collection($this->whenLoaded('costItems')),
            'status_history' => StatusHistoryResource::collection($this->whenLoaded('statusHistory')),
            'created_at' => $this->created_at,
        ];
    }
}
