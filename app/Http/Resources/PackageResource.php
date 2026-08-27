<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'purchase_request' => new PurchaseRequestResource($this->whenLoaded('purchaseRequest')),
            'store' => $this->store,
            'original_tracking' => $this->original_tracking,
            'received_at' => $this->received_at?->toDateString(),
            'weight_lb' => (float) $this->weight_lb,
            'photo_url' => $this->photo_path ? asset('storage/'.$this->photo_path) : null,
            'location' => $this->location,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_history' => StatusHistoryResource::collection($this->whenLoaded('statusHistory')),
            'created_at' => $this->created_at,
        ];
    }
}
