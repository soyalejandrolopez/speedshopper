<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\PurchaseRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseRequestFactory extends Factory
{
    protected $model = PurchaseRequest::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'product_name' => fake()->words(3, true),
            'product_url' => fake()->url(),
            'store' => fake()->company(),
            'description' => fake()->sentence(),
            'size_color' => fake()->randomElement(['S', 'M', 'L', 'XL']),
            'quantity' => fake()->numberBetween(1, 3),
            'unit_price' => fake()->randomFloat(2, 10, 200),
            'status' => 'new',
        ];
    }
}
