<?php

namespace App\Constants;

final class FootballConstants
{
    
    public const POINTS_WIN = 3;
    public const POINTS_DRAW = 1;
    public const POINTS_LOSS = 0;

    
    public const STAT_PLAYED = 1;
    public const STAT_WINS = 1;
    public const STAT_DRAWS = 1;
    public const STAT_LOSSES = 1;

    
    public const MIN_TEAM_POWER = 80.00;
    public const MAX_TEAM_POWER = 95.00;

    
    public const HOME_ADVANTAGE_BONUS_FACTOR = 1.05;
    public const AWAY_ADVANTAGE_BONUS_FACTOR = 0.95;

    public const MAX_POTENTIAL_GOALS = 6;
    public const GOAL_PROBABILITY_DENOMINATOR = 100.0;

    public const GOALS_TO_SHOOTS_MIN = 4;
    public const GOALS_TO_SHOOTS_MAX = 8;
    public const RANDOM_SHOOTS_MAX_BONUS = 5;

    public const HOME_ADVANTAGE_BOOST_FACTOR = 1.05;
    public const MORALE_BOOST_FACTOR = 1.02;
    public const AWAY_WIN_RATE_THRESHOLD = 0.4;
    public const AWAY_WIN_BONUS_FACTOR = 1.03;

    
    public const PREDICTION_WEIGHT_GOAL_SHOOT_RATIO = 2;
    public const PREDICTION_WEIGHT_AWAY_PERFORMANCE = 1.5;
    public const OPTIMAL_GOAL_SHOOT_RATIO = 0.2;
    public const PREDICTION_LAST_WEEKS = 3;
    public const PREDICTION_WEIGHT_POINTS = 10;

}
