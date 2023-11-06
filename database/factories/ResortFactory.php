<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ResortFactory extends Factory
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
            'ben_bus_code' => fake()->countryCode,
            'ski_club_id' => mt_rand(1,500),
        ];
    }
}
