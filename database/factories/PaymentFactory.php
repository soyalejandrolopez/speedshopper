<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $total = fake()->randomFloat(2, 50, 500);

        return [
            'customer_id' => Customer::factory(),
            'invoice_total' => $total,
            'amount_paid' => fake()->randomFloat(2, 0, $total),
            'payment_method' => fake()->randomElement(['cash', 'zelle', 'card', 'paypal', 'bank_transfer']),
            'paid_at' => fake()->dateTimeThisYear(),
        ];
    }
}
