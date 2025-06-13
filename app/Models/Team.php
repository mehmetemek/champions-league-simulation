<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'power'];

    public function scoreBoard()
    {
        return $this->hasOne(ScoreBoard::class);
    }
}