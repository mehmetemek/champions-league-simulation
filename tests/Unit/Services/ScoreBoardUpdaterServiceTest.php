<?php

namespace Tests\Unit\Services;

use App\Models\Fixture;
use App\Models\Game;
use App\Models\ScoreBoard;
use App\Models\Team;
use App\Services\ScoreBoardUpdaterService;
use App\Constants\FootballConstants;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoreBoardUpdaterServiceTest extends TestCase
{
    use RefreshDatabase;
    
    protected $scoreBoardUpdaterService;
    
    public function setUp(): void
    {
        parent::setUp();
        
        $this->scoreBoardUpdaterService = new ScoreBoardUpdaterService();
    }
    
    public function test_update_score_boards_for_home_win()
    {
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();
        
        $fixture = Fixture::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'week' => 1
        ]);
        
        $game = Game::factory()->create([
            'fixture_id' => $fixture->id,
            'home_score' => 3,
            'away_score' => 1,
            'home_shoot_count' => 10,
            'away_shoot_count' => 5,
        ]);
        
        $this->scoreBoardUpdaterService->updateScoreBoards($game);
        
        $homeScoreBoard = ScoreBoard::where('team_id', $homeTeam->id)->first();
        $awayScoreBoard = ScoreBoard::where('team_id', $awayTeam->id)->first();
        
        $this->assertEquals(FootballConstants::STAT_PLAYED, $homeScoreBoard->played);
        $this->assertEquals(FootballConstants::STAT_PLAYED, $awayScoreBoard->played);
        
        $this->assertEquals(FootballConstants::STAT_WINS, $homeScoreBoard->wins);
        $this->assertEquals(0, $homeScoreBoard->draws);
        $this->assertEquals(0, $homeScoreBoard->losses);
        
        $this->assertEquals(0, $awayScoreBoard->wins);
        $this->assertEquals(0, $awayScoreBoard->draws);
        $this->assertEquals(FootballConstants::STAT_LOSSES, $awayScoreBoard->losses);
        
        $this->assertEquals(3, $homeScoreBoard->goals_for);
        $this->assertEquals(1, $homeScoreBoard->goals_against);
        $this->assertEquals(2, $homeScoreBoard->goal_difference);
        
        $this->assertEquals(1, $awayScoreBoard->goals_for);
        $this->assertEquals(3, $awayScoreBoard->goals_against);
        $this->assertEquals(-2, $awayScoreBoard->goal_difference);
        
        $this->assertEquals(FootballConstants::POINTS_WIN, $homeScoreBoard->points);
        $this->assertEquals(FootballConstants::POINTS_LOSS, $awayScoreBoard->points);
    }
    
    public function test_update_score_boards_for_away_win()
    {
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();
        
        $fixture = Fixture::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'week' => 1
        ]);
        
        $game = Game::factory()->create([
            'fixture_id' => $fixture->id,
            'home_score' => 0,
            'away_score' => 2,
            'home_shoot_count' => 8,
            'away_shoot_count' => 12,
        ]);
        
        $this->scoreBoardUpdaterService->updateScoreBoards($game);
        
        $homeScoreBoard = ScoreBoard::where('team_id', $homeTeam->id)->first();
        $awayScoreBoard = ScoreBoard::where('team_id', $awayTeam->id)->first();
        
        $this->assertEquals(0, $homeScoreBoard->wins);
        $this->assertEquals(0, $homeScoreBoard->draws);
        $this->assertEquals(FootballConstants::STAT_LOSSES, $homeScoreBoard->losses);
        
        $this->assertEquals(FootballConstants::STAT_WINS, $awayScoreBoard->wins);
        $this->assertEquals(0, $awayScoreBoard->draws);
        $this->assertEquals(0, $awayScoreBoard->losses);
        
        $this->assertEquals(0, $homeScoreBoard->goals_for);
        $this->assertEquals(2, $homeScoreBoard->goals_against);
        $this->assertEquals(-2, $homeScoreBoard->goal_difference);
        
        $this->assertEquals(2, $awayScoreBoard->goals_for);
        $this->assertEquals(0, $awayScoreBoard->goals_against);
        $this->assertEquals(2, $awayScoreBoard->goal_difference);
        
        $this->assertEquals(FootballConstants::POINTS_LOSS, $homeScoreBoard->points);
        $this->assertEquals(FootballConstants::POINTS_WIN, $awayScoreBoard->points);
    }
    
    public function test_update_score_boards_for_draw()
    {
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();
        
        $fixture = Fixture::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'week' => 1
        ]);
        
        $game = Game::factory()->create([
            'fixture_id' => $fixture->id,
            'home_score' => 1,
            'away_score' => 1,
            'home_shoot_count' => 10,
            'away_shoot_count' => 9,
        ]);
        
        $this->scoreBoardUpdaterService->updateScoreBoards($game);
        
        $homeScoreBoard = ScoreBoard::where('team_id', $homeTeam->id)->first();
        $awayScoreBoard = ScoreBoard::where('team_id', $awayTeam->id)->first();
        
        $this->assertEquals(0, $homeScoreBoard->wins);
        $this->assertEquals(FootballConstants::STAT_DRAWS, $homeScoreBoard->draws);
        $this->assertEquals(0, $homeScoreBoard->losses);
        
        $this->assertEquals(0, $awayScoreBoard->wins);
        $this->assertEquals(FootballConstants::STAT_DRAWS, $awayScoreBoard->draws);
        $this->assertEquals(0, $awayScoreBoard->losses);
        
        $this->assertEquals(1, $homeScoreBoard->goals_for);
        $this->assertEquals(1, $homeScoreBoard->goals_against);
        $this->assertEquals(0, $homeScoreBoard->goal_difference);
        
        $this->assertEquals(1, $awayScoreBoard->goals_for);
        $this->assertEquals(1, $awayScoreBoard->goals_against);
        $this->assertEquals(0, $awayScoreBoard->goal_difference);
        
        $this->assertEquals(FootballConstants::POINTS_DRAW, $homeScoreBoard->points);
        $this->assertEquals(FootballConstants::POINTS_DRAW, $awayScoreBoard->points);
    }
}
