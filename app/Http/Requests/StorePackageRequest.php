<?php

namespace App\Http\Requests;

use App\Enums\PackageStatus;
use App\Models\Package;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StorePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Package::class);
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'purchase_request_id' => ['nullable', 'integer', 'exists:purchase_requests,id'],
            'store' => ['nullable', 'string', 'max:255'],
            'original_tracking' => ['nullable', 'string', 'max:255'],
            'received_at' => ['nullable', 'date'],
            'weight_lb' => ['nullable', 'numeric', 'min:0'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'location' => ['nullable', 'string', 'max:100'],
            'status' => ['sometimes', new Enum(PackageStatus::class)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
