<?php

namespace Tests\Unit\Models;

use App\Models\ScoreBoard;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_has_score_board_relation()
    {
        $team = Team::factory()->create();
        $scoreBoard = ScoreBoard::factory()->create(['team_id' => $team->id]);
        
        $result = $team->scoreBoard;
        
        $this->assertNotNull($result);
        $this->assertEquals($scoreBoard->id, $result->id);
    }
}
