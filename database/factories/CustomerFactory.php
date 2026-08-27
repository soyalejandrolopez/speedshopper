<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'whatsapp' => '+'.fake()->numberBetween(10000000000, 99999999999),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'country' => fake()->randomElement(['MX', 'GT', 'HN', 'SV', 'CR', 'PA', 'CO', 'EC', 'PE', 'CL', 'AR']),
            'registered_at' => fake()->dateTimeThisYear(),
        ];
    }
}
