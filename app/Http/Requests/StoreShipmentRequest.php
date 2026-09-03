<?php

namespace App\Http\Requests;

use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Shipment::class);
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'package_ids' => ['sometimes', 'array'],
            'package_ids.*' => ['integer', 'exists:packages,id'],
            'carrier' => ['nullable', 'string', 'max:255'],
            'destination_country' => ['nullable', 'string', 'max:50'],
            'final_weight_lb' => ['nullable', 'numeric', 'min:0'],
            'dimensions' => ['nullable', 'string', 'max:100'],
            'international_tracking' => ['nullable', 'string', 'max:255'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0'],
            'shipped_at' => ['nullable', 'date'],
            'delivered_at' => ['nullable', 'date'],
            'status' => ['sometimes', new Enum(ShipmentStatus::class)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
