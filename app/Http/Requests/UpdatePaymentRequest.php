<?php

namespace App\Http\Requests;

class UpdatePaymentRequest extends StorePaymentRequest
{
    public function rules(): array
    {
        return parent::rules();
    }
}
