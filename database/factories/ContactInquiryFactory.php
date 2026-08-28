<?php

namespace Database\Factories;

use App\Models\ContactInquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactInquiry>
 */
class ContactInquiryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'country' => fake()->randomElement(['VE', 'CO', 'EC', 'PE', 'CL', 'CR', 'PA', 'DO', 'SV', 'HN', 'MX']),
            'subject' => fake()->randomElement(['general', 'quote', 'shipping', 'reception']),
            'message' => fake()->paragraph(),
            'status' => fake()->randomElement(['unread', 'read', 'contacted']),
        ];
    }
}
