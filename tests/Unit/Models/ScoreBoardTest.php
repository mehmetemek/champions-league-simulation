<?php

namespace Tests\Unit\Models;

use App\Models\ScoreBoard;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoreBoardTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_score_board_belongs_to_team()
    {
        $team = Team::factory()->create();
        $scoreBoard = ScoreBoard::factory()->create(['team_id' => $team->id]);
        
        $result = $scoreBoard->team;
        
        $this->assertInstanceOf(Team::class, $result);
        $this->assertEquals($team->id, $result->id);
    }
}
