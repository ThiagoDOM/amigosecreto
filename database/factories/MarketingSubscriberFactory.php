<?php

namespace Database\Factories;

use App\Models\MarketingSubscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketingSubscriber>
 */
class MarketingSubscriberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'name' => fake()->name(),
            'source' => 'organizer_registration',
            'ip_address' => fake()->ipv4(),
        ];
    }
}
