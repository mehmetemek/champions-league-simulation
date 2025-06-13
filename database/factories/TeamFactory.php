<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Constants\FootballConstants;

class TeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'power' => $this->faker->randomFloat(2, FootballConstants::MIN_TEAM_POWER, FootballConstants::MAX_TEAM_POWER),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
