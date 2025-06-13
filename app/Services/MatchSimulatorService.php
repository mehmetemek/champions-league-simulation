<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\Game;
use App\Models\Team;
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

        list($homePowerAdjusted, $awayPowerAdjusted) =
            $this->adjustTeamPowers($homePower, $awayPower, $homeTeam->id, $awayTeam->id);

        list($homeScore, $awayScore, $homeShootCount, $awayShootCount) =
            $this->calculateScores($homePowerAdjusted, $awayPowerAdjusted);

        $game = Game::create([
                'fixture_id' => $fixture->id,
                'home_score' => $homeScore,
                'away_score' => $awayScore,
                'home_shoot_count' => $homeShootCount,
                'away_shoot_count' => $awayShootCount,
            ]);

        $isHomeWinner = $homeScore > $awayScore;
        $isAwayWinner = $awayScore > $homeScore;

        if ($isHomeWinner) {
            $homeGoalShootRatio = $homeScore / $homeShootCount;
            $awayGoalEfficiencyPenalty = $homeGoalEfficiencyBonus = $homeGoalShootRatio * FootballConstants::PREDICTION_WEIGHT_GOAL_SHOOT_RATIO;
            
            $homePowerAdjusted += $homeGoalEfficiencyBonus;
            $awayPowerAdjusted -= $awayGoalEfficiencyPenalty;

            Team::where('id', $homeTeam->id)->update([
                'power' => $homePowerAdjusted,
            ]);

            Team::where('id', $awayTeam->id)->update([
                'power' => $awayPowerAdjusted,
            ]);
        }
        
        if ($isAwayWinner) {
            $awayGoalShootRatio = $awayScore / $awayShootCount;
            $homeGoalEfficiencyPenalty = $awayGoalEfficiencyBonus = $awayGoalShootRatio * FootballConstants::PREDICTION_WEIGHT_GOAL_SHOOT_RATIO;
            
            $awayPowerAdjusted += $awayGoalEfficiencyBonus;
            $homePowerAdjusted -= $homeGoalEfficiencyPenalty;

            Team::where('id', $awayTeam->id)->update([
                'power' => $awayPowerAdjusted,
            ]);

            Team::where('id', $homeTeam->id)->update([
                'power' => $homePowerAdjusted,
            ]);
        }

        $fixture->is_played = true;
        $fixture->save();

        return $game;
    }

    private function adjustTeamPowers(
        float $homePower,
        float $awayPower,
        int $homeTeamId,
        int $awayTeamId
    ): array {
        $homePowerAdjusted = $homePower;
        $awayPowerAdjusted = $awayPower;

        $homePowerAdjusted *= FootballConstants::HOME_ADVANTAGE_BONUS_FACTOR;

        $lastHomeGame = Game::whereHas('fixture', function ($query) use ($homeTeamId) {
                                $query->where('home_team_id', $homeTeamId)->orWhere('away_team_id', $homeTeamId);
                            })
                            ->where('updated_at', '<', Carbon::now())
                            ->orderByDesc('updated_at')
                            ->first();

        $lastAwayGame = Game::whereHas('fixture', function ($query) use ($awayTeamId) {
                                $query->where('home_team_id', $awayTeamId)->orWhere('away_team_id', $awayTeamId);
                            })
                            ->where('updated_at', '<', Carbon::now())
                            ->orderByDesc('updated_at')
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