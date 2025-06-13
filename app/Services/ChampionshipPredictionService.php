<?php

namespace App\Services;

use App\Models\ScoreBoard;
use App\Models\Fixture;
use App\Models\Team;
use App\Models\Game;
use App\Constants\FootballConstants;

class ChampionshipPredictionService
{
    public function getPredictions(int $currentWeek): array
    {
        $allTeams = Team::all();
        $totalWeeksInLeague = Fixture::max('week') ?? 0;

        $defaultPredictions = $allTeams->map(function($team) {
            return [
                'team_id' => $team->id,
                'team_name' => $team->name,
                'percentage' => 0,
            ];
        })->toArray();

        if ($totalWeeksInLeague == 0 || $currentWeek < ($totalWeeksInLeague - FootballConstants::PREDICTION_LAST_WEEKS + 1)) {
            return $defaultPredictions;
        }

        $scoreBoards = ScoreBoard::with('team')
                                 ->orderByDesc('points')
                                 ->orderByDesc('goal_difference')
                                 ->orderByDesc('goals_for')
                                 ->get();

        $predictions = [];
        $teamWeightedScores = [];

        $leader = $scoreBoards->first();
        $canCatchUp = false;

        foreach ($scoreBoards as $sb) {
            if ($sb->team_id === $leader->team_id) {
                continue;
            }

            $remainingTeamFixturesCount = Fixture::where('is_played', false)
                                                ->where(function($query) use ($sb) {
                                                    $query->where('home_team_id', $sb->team_id)
                                                          ->orWhere('away_team_id', $sb->team_id);
                                                })
                                                ->count();

            $maxPossiblePointsForSb = $sb->points + ($remainingTeamFixturesCount * FootballConstants::POINTS_WIN);

            if ($maxPossiblePointsForSb >= $leader->points) {
                $canCatchUp = true;
                break;
            }
        }

        if (!$canCatchUp) {
            foreach ($scoreBoards as $sb) {
                $team = $sb->team;
                $predictions[] = [
                    'team_id' => $team->id,
                    'team_name' => $team->name,
                    'percentage' => ($team->id === $leader->team_id) ? 100 : 0,
                ];
            }
            return $predictions;
        }

        $totalWeightedScore = 0;

        foreach ($scoreBoards as $sb) {
            $team = $sb->team;

            $weightedScore = ($sb->points * FootballConstants::PREDICTION_WEIGHT_POINTS) + $team->power;

            $weightedScore = max(0, $weightedScore);
            $teamWeightedScores[$team->id] = $weightedScore;
            $totalWeightedScore += $weightedScore;
        }

        foreach ($scoreBoards as $sb) {
            $team = $sb->team;
            $teamId = $team->id;
            $percentage = 0;

            if (isset($teamWeightedScores[$teamId])) {
                $score = $teamWeightedScores[$teamId];
                if ($totalWeightedScore > 0) {
                    $percentage = ($score / $totalWeightedScore) * 100;
                } else {
                    $percentage = 100 / $allTeams->count();
                }
            } else {
                $percentage = 0;
            }
            $predictions[] = [
                'team_id' => $team->id,
                'team_name' => $team->name,
                'percentage' => round($percentage),
            ];
        }

        $sumPercentages = array_sum(array_column($predictions, 'percentage'));
        if ($sumPercentages != 100) {
            $diff = 100 - $sumPercentages;

            if (!empty($predictions)) {
                $maxPercentage = -1;
                $maxIndex = -1;
                foreach ($predictions as $index => $item) {
                    if ($item['percentage'] > $maxPercentage) {
                        $maxPercentage = $item['percentage'];
                        $maxIndex = $index;
                    }
                }

                if ($maxIndex !== -1) {
                    $predictions[$maxIndex]['percentage'] += $diff;
                    $predictions[$maxIndex]['percentage'] = max(0, min(100, $predictions[$maxIndex]['percentage']));
                }
            }
        }

        return $predictions;
    }
}