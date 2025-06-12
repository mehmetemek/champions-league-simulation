<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\Game;
use App\Models\ScoreBoard;
use Illuminate\Support\Carbon;
use App\Constants\FootballConstants;

class MatchSimulatorService
{
    public function simulateMatch(Fixture $fixture): Game
    {
        $homeTeam = $fixture->homeTeam;
        $awayTeam = $fixture->awayTeam;

        $homePower = $homeTeam->power;
        $awayPower = $awayTeam->power;

        $homeScoreBoard = ScoreBoard::firstOrCreate(['team_id' => $homeTeam->id]);
        $awayScoreBoard = ScoreBoard::firstOrCreate(['team_id' => $awayTeam->id]);

        list($homePowerAdjusted, $awayPowerAdjusted) =
            $this->adjustTeamPowers($homePower, $awayPower, $homeTeam->id, $awayTeam->id, $homeScoreBoard, $awayScoreBoard);


        list($homeScore, $awayScore, $homeShootCount, $awayShootCount) =
            $this->calculateScores($homePowerAdjusted, $awayPowerAdjusted);

        $game = Game::create([
            'fixture_id' => $fixture->id,
            'home_score' => $homeScore,
            'away_score' => $awayScore,
            'played_at' => Carbon::now(),
            'home_shoot_count' => $homeShootCount,
            'away_shoot_count' => $awayShootCount,
        ]);

        $fixture->is_played = true;
        $fixture->save();

        return $game;
    }

    private function adjustTeamPowers(
        float $homePower,
        float $awayPower,
        int $homeTeamId,
        int $awayTeamId,
        ScoreBoard $homeScoreBoard,
        ScoreBoard $awayScoreBoard
    ): array {
        $homePowerAdjusted = $homePower;
        $awayPowerAdjusted = $awayPower;

        $homePowerAdjusted *= FootballConstants::HOME_ADVANTAGE_BONUS_FACTOR;

        $lastHomeGame = Game::whereHas('fixture', function ($query) use ($homeTeamId) {
                                $query->where('home_team_id', $homeTeamId)->orWhere('away_team_id', $homeTeamId);
                            })
                            ->where('played_at', '<', Carbon::now())
                            ->orderByDesc('played_at')
                            ->first();

        $lastAwayGame = Game::whereHas('fixture', function ($query) use ($awayTeamId) {
                                $query->where('home_team_id', $awayTeamId)->orWhere('away_team_id', $awayTeamId);
                            })
                            ->where('played_at', '<', Carbon::now())
                            ->orderByDesc('played_at')
                            ->first();

        if ($lastHomeGame) {
            $isHomeWinner = ($lastHomeGame->fixture->home_team_id === $homeTeamId && $lastHomeGame->home_score > $lastHomeGame->away_score) ||
                            ($lastHomeGame->fixture->away_team_id === $homeTeamId && $lastHomeGame->away_score > $lastHomeGame->home_score);
            if ($isHomeWinner) {
                $homePowerAdjusted *= FootballConstants::MORALE_BOOST_FACTOR;
            }
        }
        if ($lastAwayGame) {
            $isAwayWinner = ($lastAwayGame->fixture->home_team_id === $awayTeamId && $lastAwayGame->home_score > $lastAwayGame->away_score) ||
                            ($lastAwayGame->fixture->away_team_id === $awayTeamId && $lastAwayGame->away_score > $lastAwayGame->home_score);
            if ($isAwayWinner) {
                $awayPowerAdjusted *= FootballConstants::MORALE_BOOST_FACTOR;
            }
        }

        if ($awayScoreBoard->played > 0) {
            $awayWinsCount = Game::whereHas('fixture', function ($query) use ($awayTeamId) {
                                    $query->where('away_team_id', $awayTeamId);
                                })
                                ->whereColumn('away_score', '>', 'home_score')
                                ->count();

            $awayPlayedCount = Game::whereHas('fixture', function ($query) use ($awayTeamId) {
                                    $query->where('away_team_id', $awayTeamId);
                                })->count();

            if ($awayPlayedCount > 0) {
                $awayWinRate = $awayWinsCount / $awayPlayedCount;
                if ($awayWinRate > FootballConstants::AWAY_WIN_RATE_THRESHOLD) {
                    $awayPowerAdjusted *= FootballConstants::AWAY_WIN_BONUS_FACTOR;
                }
            }
        }

        return [$homePowerAdjusted, $awayPowerAdjusted];
    }

    private function calculateScores(float $homePower, float $awayPower): array
    {
        $homeScore = 0;
        $awayScore = 0;

        $denominator = FootballConstants::GOAL_PROBABILITY_DENOMINATOR;

        $homeGoalProbability = $homePower / ($homePower + $awayPower);
        $awayGoalProbability = $awayPower / ($homePower + $awayPower);

        for ($i = 0; $i < FootballConstants::MAX_POTENTIAL_GOALS; $i++) {
            if (mt_rand(0, $denominator) / $denominator < $homeGoalProbability) {
                $homeScore++;
            }
            if (mt_rand(0, $denominator) / $denominator < $awayGoalProbability) {
                $awayScore++;
            }
        }

        $homeShootCount = $homeScore * mt_rand(FootballConstants::GOALS_TO_SHOOTS_MIN, FootballConstants::GOALS_TO_SHOOTS_MAX);
        $awayShootCount = $awayScore * mt_rand(FootballConstants::GOALS_TO_SHOOTS_MIN, FootballConstants::GOALS_TO_SHOOTS_MAX);

        $homeShootCount += mt_rand(0, FootballConstants::RANDOM_SHOOTS_MAX_BONUS);
        $awayShootCount += mt_rand(0, FootballConstants::RANDOM_SHOOTS_MAX_BONUS);

        return [$homeScore, $awayScore, $homeShootCount, $awayShootCount];
    }
}