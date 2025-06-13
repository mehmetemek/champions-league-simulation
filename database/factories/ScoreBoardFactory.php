<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScoreBoardFactory extends Factory
{
    public function definition(): array
    {
        $team = Team::factory()->create();
        $played = $this->faker->numberBetween(0, 10);
        $wins = $this->faker->numberBetween(0, $played);
        $draws = $this->faker->numberBetween(0, $played - $wins);
        $losses = $played - $wins - $draws;
        $goalsFor = $wins * 2 + $draws;
        $goalsAgainst = $losses * 2 + $draws;
        $points = $wins * 3 + $draws;
        
        return [
            'team_id' => $team->id,
            'played' => $played,
            'wins' => $wins,
            'draws' => $draws,
            'losses' => $losses,
            'goals_for' => $goalsFor,
            'goals_against' => $goalsAgainst,
            'goal_difference' => $goalsFor - $goalsAgainst,
            'points' => $points,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
