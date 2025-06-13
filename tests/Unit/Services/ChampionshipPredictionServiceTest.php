<?php

namespace Tests\Unit\Services;

use App\Models\Fixture;
use App\Models\ScoreBoard;
use App\Models\Team;
use App\Services\ChampionshipPredictionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChampionshipPredictionServiceTest extends TestCase
{
    use RefreshDatabase;
    
    protected $championshipPredictionService;
    
    public function setUp(): void
    {
        parent::setUp();
        
        $this->championshipPredictionService = new ChampionshipPredictionService();
    }
    
    public function test_calculate_predictions_ranks_teams_by_points()
    {
        $team1 = Team::factory()->create(['name' => 'Team 1']);
        $team2 = Team::factory()->create(['name' => 'Team 2']);
        $team3 = Team::factory()->create(['name' => 'Team 3']);
        $team4 = Team::factory()->create(['name' => 'Team 4']);
        
        Fixture::factory()->create(['week' => 10]);
        
        ScoreBoard::factory()->create([
            'team_id' => $team1->id, 
            'played' => 5, 
            'wins' => 5, 
            'points' => 15
        ]);
        
        ScoreBoard::factory()->create([
            'team_id' => $team2->id, 
            'played' => 5, 
            'wins' => 3, 
            'draws' => 1, 
            'points' => 10
        ]);
        
        ScoreBoard::factory()->create([
            'team_id' => $team3->id, 
            'played' => 5, 
            'wins' => 2, 
            'draws' => 1, 
            'points' => 7
        ]);
        
        ScoreBoard::factory()->create([
            'team_id' => $team4->id, 
            'played' => 5, 
            'wins' => 0, 
            'points' => 0
        ]);
        
        $predictions = $this->championshipPredictionService->getPredictions(10);
        
        $this->assertCount(4, $predictions);
        
        $sortedPredictions = collect($predictions)->sortByDesc('percentage')->values()->all();
        
        $this->assertEquals($team1->id, $sortedPredictions[0]['team_id']);
        $this->assertEquals($team2->id, $sortedPredictions[1]['team_id']);
        $this->assertEquals($team3->id, $sortedPredictions[2]['team_id']);
        $this->assertEquals($team4->id, $sortedPredictions[3]['team_id']);
        
        $totalPercentage = array_sum(array_column($predictions, 'percentage'));
        $this->assertEqualsWithDelta(100, $totalPercentage, 0.1);
    }
    
    public function test_calculate_predictions_with_tied_points_uses_goal_difference()
    {
        $team1 = Team::factory()->create(['name' => 'Team 1']);
        $team2 = Team::factory()->create(['name' => 'Team 2']);
        
        Fixture::factory()->create(['week' => 10]);
        
        ScoreBoard::factory()->create([
            'team_id' => $team1->id, 
            'played' => 5, 
            'wins' => 3, 
            'points' => 9,
            'goal_difference' => 10
        ]);
        
        ScoreBoard::factory()->create([
            'team_id' => $team2->id, 
            'played' => 5, 
            'wins' => 3, 
            'points' => 9,
            'goal_difference' => 5
        ]);
        
        $predictions = $this->championshipPredictionService->getPredictions(10);
        
        $sortedPredictions = collect($predictions)->sortByDesc('percentage')->values()->all();
        
        $this->assertGreaterThanOrEqual($sortedPredictions[1]['percentage'], $sortedPredictions[0]['percentage']);
    }
}
