<?php

namespace App\Constants;

final class FootballConstants
{
    // Maç sonuçları için puanlar
    public const POINTS_WIN = 3;
    public const POINTS_DRAW = 1;
    public const POINTS_LOSS = 0;

    // Skor tablosu istatistik artışları
    public const STAT_PLAYED = 1;
    public const STAT_WINS = 1;
    public const STAT_DRAWS = 1;
    public const STAT_LOSSES = 1;

    // Minimum ve maksimum takım gücü puanları (seeder için)
    public const MIN_TEAM_POWER = 80.00;
    public const MAX_TEAM_POWER = 95.00;

    // Maç simülasyonu için sabitler
    public const HOME_ADVANTAGE_BONUS_FACTOR = 1.05; // Ev sahibine %5 güç bonusu
    public const AWAY_ADVANTAGE_BONUS_FACTOR = 0.95; // Deplasman takımına %5 dezavantaj

    public const MAX_POTENTIAL_GOALS = 6; // Bir maçta atılabilecek maksimum olası gol sayısı
    public const GOAL_PROBABILITY_DENOMINATOR = 1000.0; // Rastgele sayı üretimi için payda
    
    public const GOALS_TO_SHOOTS_MIN = 4;
    public const GOALS_TO_SHOOTS_MAX = 8;
    public const RANDOM_SHOOTS_MAX_BONUS = 5;

    // Şampiyonluk tahmini için sabitler
    public const PREDICTION_LAST_WEEKS = 3; // Son kaç haftada tahmin yapılacağı
    public const PREDICTION_WEIGHT_POINTS = 10; // Tahmin algoritmasında puanların ağırlığı
    public const PREDICTION_WEIGHT_POWER = 1; // Tahmin algoritmasında takım gücünün ağırlığı


    // Maç simülasyonu için sabitler
    public const HOME_ADVANTAGE_BOOST_FACTOR = 1.05; // Daha açıklayıcı isim
    public const MORALE_BOOST_FACTOR = 1.02; // Son maçı kazananlara %2 moral bonusu
    public const AWAY_WIN_RATE_THRESHOLD = 0.4; // Deplasman galibiyet oranı eşiği (%40)
    public const AWAY_WIN_BONUS_FACTOR = 1.03; // Eşiği geçenlere %3 deplasman bonusu

    // Şampiyonluk tahmini için sabitler
    public const PREDICTION_WEIGHT_GOAL_SHOOT_RATIO = 2; // Gol/şut oranı için ağırlık
    public const PREDICTION_WEIGHT_AWAY_PERFORMANCE = 1.5; // Deplasman performansı için ağırlık
    public const OPTIMAL_GOAL_SHOOT_RATIO = 0.2; // İdeal gol/şut oranı (örn. %20'lik bir verimlilik)

}