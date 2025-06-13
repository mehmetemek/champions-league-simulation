<?php

namespace Tests\Unit\Models;

use App\Models\Fixture;
use App\Models\Game;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FixtureTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_fixture_belongs_to_home_team()
    {
        $homeTeam = Team::factory()->create();
        $fixture = Fixture::factory()->create(['home_team_id' => $homeTeam->id]);
        
        $result = $fixture->homeTeam;
        
        $this->assertInstanceOf(Team::class, $result);
        $this->assertEquals($homeTeam->id, $result->id);
    }
    
    public function test_fixture_belongs_to_away_team()
    {
        $awayTeam = Team::factory()->create();
        $fixture = Fixture::factory()->create(['away_team_id' => $awayTeam->id]);
        
        $result = $fixture->awayTeam;
        
        $this->assertInstanceOf(Team::class, $result);
        $this->assertEquals($awayTeam->id, $result->id);
    }
    
    public function test_fixture_has_game_relation()
    {
        $fixture = Fixture::factory()->create();
        $game = Game::factory()->create(['fixture_id' => $fixture->id]);
        
        $result = $fixture->game;
        
        $this->assertInstanceOf(Game::class, $result);
        $this->assertEquals($game->id, $result->id);
    }
}
