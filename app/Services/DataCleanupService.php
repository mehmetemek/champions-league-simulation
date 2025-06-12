<?php

namespace App\Services;

use App\Models\Team;
use App\Models\Fixture;
use App\Models\Game;
use App\Models\ScoreBoard;
use Illuminate\Support\Facades\DB;

class DataCleanupService
{
    public function cleanupTournamentData(bool $includeTeam = true): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        Game::truncate();
        ScoreBoard::truncate();
        Fixture::truncate();

        if ($includeTeam) {
            Team::truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
