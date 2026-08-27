<?php

namespace App\Http\Requests;

use App\Enums\RequestStatus;
use App\Models\PurchaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StorePurchaseRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', PurchaseRequest::class) ?? true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => [
                'required',
                'integer',
                'exists:customers,id',
            ],
            'product_name' => ['required', 'string', 'max:255'],
            'product_url' => ['nullable', 'url:http,https', 'max:2048'],
            'store' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'size_color' => ['nullable', 'string', 'max:255'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:999'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'discount_found' => ['nullable', 'numeric', 'min:0'],
            'status' => ['sometimes', new Enum(RequestStatus::class)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
