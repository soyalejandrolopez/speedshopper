<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdatePurchaseRequestRequest extends StorePurchaseRequestRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['customer_id'][] = Rule::exists('customers', 'id');

        return $rules;
    }
}
