<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Services\DataCleanupService;
use Illuminate\Database\Seeder;
use App\Constants\FootballConstants;
use Faker\Factory as Faker;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        $cleanupService = app(DataCleanupService::class);
        $cleanupService->cleanupTournamentData();
        
        $teamsData = [
            ['name' => 'Chelsea'],
            ['name' => 'Arsenal'],
            ['name' => 'Manchester City'],
            ['name' => 'Liverpool']
        ];

        foreach ($teamsData as $teamData) {
            $power = $faker->randomFloat(2, FootballConstants::MIN_TEAM_POWER, FootballConstants::MAX_TEAM_POWER);

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