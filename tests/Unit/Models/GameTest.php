<?php

namespace Tests\Unit\Models;

use App\Models\Fixture;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_game_belongs_to_fixture()
    {
        $fixture = Fixture::factory()->create();
        $game = Game::factory()->create(['fixture_id' => $fixture->id]);
        
        $result = $game->fixture;
        
        $this->assertInstanceOf(Fixture::class, $result);
        $this->assertEquals($fixture->id, $result->id);
    }
}
