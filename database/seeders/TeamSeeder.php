<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\Fixture;
use App\Models\Game;
use App\Models\ScoreBoard;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        Game::truncate();
        ScoreBoard::truncate();
        Fixture::truncate();
        Team::truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $teamsData = [
            ['name' => 'Chelsea'],
            ['name' => 'Arsenal'],
            ['name' => 'Manchester City'],
            ['name' => 'Liverpool']
        ];

        foreach ($teamsData as $teamData) {
            $power = $faker->randomFloat(2, 80, 95);

            $team = Team::create([
                'name' => $teamData['name'],
                'power' => $power,
            ]);

            $team->scoreBoard()->create([
                'played' => 0,
                'wins' => 0,
                'draws' => 0,
                'losses' => 0,
                'goals_for' => 0,
                'goals_against' => 0,
                'goal_difference' => 0,
                'points' => 0,
            ]);
        }
    }
}