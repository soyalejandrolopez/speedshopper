<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    protected $model = Package::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'store' => fake()->company(),
            'original_tracking' => '1Z'.fake()->numerify('#########'),
            'received_at' => fake()->dateTimeThisMonth(),
            'weight_lb' => fake()->randomFloat(2, 0.5, 30),
            'location' => 'Estante '.fake()->randomLetter().'-'.fake()->randomDigit(),
            'status' => 'received',
        ];
    }
}
