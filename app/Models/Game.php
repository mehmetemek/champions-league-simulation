<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'fixture_id',
        'home_score',
        'away_score',
        'played_at',
        'home_shoot_count',
        'away_shoot_count',
    ];
    
    protected $casts = [
        'played_at' => 'datetime',
    ];

    public function fixture()
    {
        return $this->belongsTo(Fixture::class);
    }
}