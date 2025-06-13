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

    
    public const MIN_TEAM_POWER = 70.00;
    public const MAX_TEAM_POWER = 95.00;

    
    public const HOME_ADVANTAGE_BONUS_FACTOR = 1.10;
    public const AWAY_ADVANTAGE_BONUS_FACTOR = 0.90;

    public const MAX_POTENTIAL_GOALS = 8;
    public const GOAL_PROBABILITY_DENOMINATOR = 100.0;

    public const GOALS_TO_SHOOTS_MIN = 5;
    public const GOALS_TO_SHOOTS_MAX = 20;
    public const RANDOM_SHOOTS_MAX_BONUS = 5;

    public const MORALE_BOOST_FACTOR = 1.10;
    
    public const PREDICTION_WEIGHT_GOAL_SHOOT_RATIO = 5.5;
    public const PREDICTION_LAST_WEEKS = 3;
    public const PREDICTION_WEIGHT_POINTS = 10;

}
