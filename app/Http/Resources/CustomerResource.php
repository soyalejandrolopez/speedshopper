<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'notes' => $this->when($request->user()?->isAdmin(), $this->notes),
            'registered_at' => $this->registered_at?->toDateString(),
            'balance_due' => $this->whenAppended('balance_due'),
            'created_at' => $this->created_at,
        ];
    }
}
