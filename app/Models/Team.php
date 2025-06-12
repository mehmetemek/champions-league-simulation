<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'power'];

    public function homeFixtures()
    {
        return $this->hasMany(Fixture::class, 'home_team_id');
    }

    public function awayFixtures()
    {
        return $this->hasMany(Fixture::class, 'away_team_id');
    }
    
    public function homeGames()
    {
        return $this->hasManyThrough(
            Game::class,
            Fixture::class,
            'home_team_id',
            'fixture_id',
            'id',
            'id'
        );
    }

    public function awayGames()
    {
        return $this->hasManyThrough(
            Game::class,
            Fixture::class,
            'away_team_id',
            'fixture_id',
            'id',
            'id'
        );
    }

    public function scoreBoard()
    {
        return $this->hasOne(ScoreBoard::class);
    }
}