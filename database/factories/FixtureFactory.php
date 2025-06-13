<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class FixtureFactory extends Factory
{
    public function definition(): array
    {
        $teams = Team::factory()->count(2)->create();
        
        return [
            'week' => $this->faker->numberBetween(1, 10),
            'home_team_id' => $teams[0]->id,
            'away_team_id' => $teams[1]->id,
            'is_played' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
