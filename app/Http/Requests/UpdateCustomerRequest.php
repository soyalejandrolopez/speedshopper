<?php

namespace App\Http\Requests;

class UpdateCustomerRequest extends StoreCustomerRequest
{
    public function rules(): array
    {
        return parent::rules();
    }
}
