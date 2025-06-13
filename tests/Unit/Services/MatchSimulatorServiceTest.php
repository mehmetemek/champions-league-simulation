<?php

namespace Tests\Unit\Services;

use App\Models\Fixture;
use App\Models\Game;
use App\Models\Team;
use App\Services\MatchSimulatorService;
use App\Constants\FootballConstants;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchSimulatorServiceTest extends TestCase
{
    use RefreshDatabase;
    
    protected $matchSimulatorService;
    
    public function setUp(): void
    {
        parent::setUp();
        
        $this->matchSimulatorService = new MatchSimulatorService();
    }

    public function test_simulate_match_creates_game_record()
    {
        $homeTeam = Team::factory()->create(['power' => FootballConstants::MIN_TEAM_POWER]);
        $awayTeam = Team::factory()->create(['power' => FootballConstants::MAX_TEAM_POWER]);
        
        $fixture = Fixture::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'week' => 1
        ]);
        
        $game = $this->matchSimulatorService->simulateMatch($fixture);
        
        $this->assertInstanceOf(Game::class, $game);
        $this->assertEquals($fixture->id, $game->fixture_id);
        $this->assertNotNull($game->home_score);
        $this->assertNotNull($game->away_score);
    }
    
    public function test_home_team_advantage_in_match_simulation()
    {
        $homeTeam = Team::factory()->create(['power' => FootballConstants::MIN_TEAM_POWER]);
        $awayTeam = Team::factory()->create(['power' => FootballConstants::MIN_TEAM_POWER]);
        
        $fixture = Fixture::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'week' => 1
        ]);
        
        $homeWins = 0;
        $awayWins = 0;
        $draws = 0;
        $sampleSize = 100;
        
        for ($i = 0; $i < $sampleSize; $i++) {
            $game = $this->matchSimulatorService->simulateMatch($fixture);
            
            if ($game->home_score > $game->away_score) {
                $homeWins++;
            } elseif ($game->home_score < $game->away_score) {
                $awayWins++;
            } else {
                $draws++;
            }
            
            $game->delete();
            
            $homeTeam->refresh();
            $awayTeam->refresh();
            $homeTeam->power = FootballConstants::MIN_TEAM_POWER;
            $awayTeam->power = FootballConstants::MIN_TEAM_POWER;
            $homeTeam->save();
            $awayTeam->save();
        }
        
        $this->assertGreaterThan($awayWins, $homeWins);
    }
    
    public function test_team_power_affects_match_outcome()
    {
        $strongTeam = Team::factory()->create(['power' => FootballConstants::MAX_TEAM_POWER]);
        $weakTeam = Team::factory()->create(['power' => FootballConstants::MIN_TEAM_POWER]);
        
        $homeFixture = Fixture::factory()->create([
            'home_team_id' => $strongTeam->id,
            'away_team_id' => $weakTeam->id,
            'week' => 1
        ]);
        
        $awayFixture = Fixture::factory()->create([
            'home_team_id' => $weakTeam->id,
            'away_team_id' => $strongTeam->id,
            'week' => 2
        ]);
        
        $strongTeamWins = 0;
        $sampleSize = 100;
        
        for ($i = 0; $i < $sampleSize; $i++) {
            $homeGame = $this->matchSimulatorService->simulateMatch($homeFixture);
            if ($homeGame->home_score > $homeGame->away_score) {
                $strongTeamWins++;
            }
            $homeGame->delete();
            
            $awayGame = $this->matchSimulatorService->simulateMatch($awayFixture);
            if ($awayGame->away_score > $awayGame->home_score) {
                $strongTeamWins++;
            }
            $awayGame->delete();
            
            $strongTeam->refresh();
            $weakTeam->refresh();
            $strongTeam->power = FootballConstants::MAX_TEAM_POWER;
            $weakTeam->power = FootballConstants::MIN_TEAM_POWER;
            $strongTeam->save();
            $weakTeam->save();
        }
        
        $this->assertGreaterThan($sampleSize, $strongTeamWins);
    }
}
