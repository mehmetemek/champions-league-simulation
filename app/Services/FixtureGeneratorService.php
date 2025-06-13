<?php

namespace App\Services;

use App\Models\Team;
use App\Models\Fixture;
use App\Models\ScoreBoard;
use App\Services\DataCleanupService;

class FixtureGeneratorService
{
    protected $cleanupService;

    public function __construct(DataCleanupService $cleanupService)
    {
        $this->cleanupService = $cleanupService;
    }


    public function generate(): bool
    {
        
        $teams = Team::all();
        $teamsCount = $teams->count();

        if ($teamsCount < 2 || $teamsCount % 2 != 0) {
            return false;
        }

        $this->cleanupService->cleanupTournamentData(false);

        $teamIds = $teams->pluck('id')->toArray();

        $fixtures = [];
        $totalWeeks = ($teamsCount - 1) * 2;

        for ($week = 1; $week <= $totalWeeks; $week++) {
            $currentRoundFixtures = [];
            $teamsInRound = $teamIds;

            for ($i = 0; $i < $teamsCount / 2; $i++) {
                $homeTeamId = $teamsInRound[$i];
                $awayTeamId = $teamsInRound[$teamsCount - 1 - $i];

                $currentRoundFixtures[] = [
                    'week' => $week,
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                ];
            }

            $fixtures = array_merge($fixtures, $currentRoundFixtures);

            $firstTeam = array_shift($teamIds);
            $lastTeam = array_pop($teamIds);
            array_unshift($teamIds, $lastTeam);
            array_splice($teamIds, 1, 0, [$firstTeam]);
        }
        
        ScoreBoard::insert($teams->map(function ($team) {
            return [
                'team_id' => $team->id
            ];
        })->toArray());
        
        Fixture::insert($fixtures);

        return true;
    }
}