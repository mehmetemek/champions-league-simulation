<?php

namespace Database\Factories;

use App\Models\Fixture;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameFactory extends Factory
{
    public function definition(): array
    {
        $fixture = Fixture::factory()->create();
        
        return [
            'fixture_id' => $fixture->id,
            'home_score' => $this->faker->numberBetween(0, 5),
            'away_score' => $this->faker->numberBetween(0, 5),
            'home_shoot_count' => $this->faker->numberBetween(5, 20),
            'away_shoot_count' => $this->faker->numberBetween(5, 20),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
