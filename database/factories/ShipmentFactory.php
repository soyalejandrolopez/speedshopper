<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'carrier' => fake()->randomElement(['DHL Express', 'FedEx', 'UPS', 'USPS']),
            'destination_country' => fake()->randomElement(['MX', 'GT', 'HN', 'SV', 'CR', 'PA', 'CO', 'EC', 'PE', 'CL', 'AR']),
            'final_weight_lb' => fake()->randomFloat(2, 1, 50),
            'dimensions' => '12x10x8 in',
            'shipping_cost' => fake()->randomFloat(2, 30, 150),
            'status' => 'draft',
        ];
    }
}
