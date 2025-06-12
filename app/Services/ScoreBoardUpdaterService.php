<?php

namespace App\Services;

use App\Models\Game;
use App\Models\ScoreBoard;
use App\Constants\FootballConstants;

class ScoreBoardUpdaterService
{

    public function updateScoreBoards(Game $game): void
    {
        $fixture = $game->fixture;
        $homeTeamId = $fixture->home_team_id;
        $awayTeamId = $fixture->away_team_id;

        $homeScoreBoard = ScoreBoard::firstOrCreate(['team_id' => $homeTeamId]);
        $awayScoreBoard = ScoreBoard::firstOrCreate(['team_id' => $awayTeamId]);

        $homeScoreBoard->played += FootballConstants::STAT_PLAYED;
        $awayScoreBoard->played += FootballConstants::STAT_PLAYED;

        $homeScoreBoard->goals_for += $game->home_score;
        $homeScoreBoard->goals_against += $game->away_score;
        $awayScoreBoard->goals_for += $game->away_score;
        $awayScoreBoard->goals_against += $game->home_score;

        if ($game->home_score > $game->away_score) {
            $homeScoreBoard->wins += FootballConstants::STAT_WINS;
            $homeScoreBoard->points += FootballConstants::POINTS_WIN;
            $awayScoreBoard->losses += FootballConstants::STAT_LOSSES;
            $awayScoreBoard->points += FootballConstants::POINTS_LOSS;
        } elseif ($game->home_score < $game->away_score) {
            $awayScoreBoard->wins += FootballConstants::STAT_WINS;
            $awayScoreBoard->points += FootballConstants::POINTS_WIN;
            $homeScoreBoard->losses += FootballConstants::STAT_LOSSES;
            $homeScoreBoard->points += FootballConstants::POINTS_LOSS;
        } else {
            $homeScoreBoard->draws += FootballConstants::STAT_DRAWS;
            $homeScoreBoard->points += FootballConstants::POINTS_DRAW;
            $awayScoreBoard->draws += FootballConstants::STAT_DRAWS;
            $awayScoreBoard->points += FootballConstants::POINTS_DRAW;
        }

        $homeScoreBoard->goal_difference = $homeScoreBoard->goals_for - $homeScoreBoard->goals_against;
        $awayScoreBoard->goal_difference = $awayScoreBoard->goals_for - $awayScoreBoard->goals_against;

        $homeScoreBoard->save();
        $awayScoreBoard->save();
    }
}