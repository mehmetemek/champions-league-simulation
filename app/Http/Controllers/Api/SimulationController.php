<?php

namespace App\Http\Controllers\Api;

use App\Constants\FootballConstants;
use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Models\ScoreBoard;
use App\Models\Team;
use App\Models\Game;
use App\Http\Resources\FixtureResource;
use App\Http\Resources\ScoreBoardResource;
use App\Http\Resources\GameResource;
use App\Services\MatchSimulatorService;
use App\Services\ScoreBoardUpdaterService;
use App\Services\ChampionshipPredictionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
class SimulationController extends Controller
{
    protected $matchSimulatorService;
    protected $scoreBoardUpdaterService;
    protected $championshipPredictionService;

    public function __construct(
        MatchSimulatorService $matchSimulatorService,
        ScoreBoardUpdaterService $scoreBoardUpdaterService,
        ChampionshipPredictionService $championshipPredictionService
    ) {
        $this->matchSimulatorService = $matchSimulatorService;
        $this->scoreBoardUpdaterService = $scoreBoardUpdaterService;
        $this->championshipPredictionService = $championshipPredictionService;
    }


    public function playWeek(Request $request, int $week)
    {
        $maxWeek = Fixture::max('week') ?? 0;
        if ($week <= 0 || $week > $maxWeek) {
            return response()->json(['message' => 'Invalid week number.'], 400);
        }

        $fixturesToPlay = Fixture::where('week', $week)
                                  ->where('is_played', false)
                                  ->with(['homeTeam', 'awayTeam'])
                                  ->get();

        if ($fixturesToPlay->isEmpty()) {
            return response()->json(['message' => 'No unplayed matches found for week ' . $week . '.'], 200);
        }

        $playedGames = [];
        foreach ($fixturesToPlay as $fixture) {
            $game = $this->matchSimulatorService->simulateMatch($fixture);
            $this->scoreBoardUpdaterService->updateScoreBoards($game);
            $playedGames[] = $game;
        }

        $scoreBoards = ScoreBoard::with('team')->orderByDesc('points')->orderByDesc('goal_difference')->get();
        $predictions = $this->championshipPredictionService->getPredictions($week);

        return response()->json([
            'current_week' => $week,
            'played_matches' => GameResource::collection($playedGames),
            'league_table' => ScoreBoardResource::collection($scoreBoards),
            'championship_predictions' => $predictions,
            'message' => 'Week ' . $week . ' successfully simulated.'
        ]);
    }

    public function playAllWeeks()
    {
        $maxWeek = Fixture::max('week') ?? 0;
        $currentPlayedWeek = Fixture::where('is_played', true)->max('week') ?? 0;

        if ($currentPlayedWeek >= $maxWeek) {
            return response()->json(['message' => 'All matches have already been played.'], 200);
        }

        $allPlayedGames = [];
        $predictions = [];

        for ($week = $currentPlayedWeek + 1; $week <= $maxWeek; $week++) {
            $fixturesToPlay = Fixture::where('week', $week)
                                      ->where('is_played', false)
                                      ->with(['homeTeam', 'awayTeam'])
                                      ->get();

            foreach ($fixturesToPlay as $fixture) {
                $game = $this->matchSimulatorService->simulateMatch($fixture);
                $this->scoreBoardUpdaterService->updateScoreBoards($game);
                $allPlayedGames[] = $game;
            }

            $predictions = $this->championshipPredictionService->getPredictions($week);
        }

        $scoreBoards = ScoreBoard::with('team')->orderByDesc('points')->orderByDesc('goal_difference')->get();

        return response()->json([
            'current_week' => $maxWeek,
            'played_matches' => GameResource::collection($allPlayedGames),
            'league_table' => ScoreBoardResource::collection($scoreBoards),
            'championship_predictions' => $predictions,
            'message' => 'All remaining weeks were successfully simulated.'
        ]);
    }


    public function resetData()
    {
        $faker = Faker::create();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Game::truncate();
        Fixture::query()->update(['is_played' => false]);

        $teams = Team::all();
        foreach ($teams as $team) {
            $team->power = $faker->randomFloat(2, FootballConstants::MIN_TEAM_POWER, FootballConstants::MAX_TEAM_POWER);
            $team->save();
        }

        ScoreBoard::query()->update([
            'played' => 0,
            'wins' => 0,
            'draws' => 0,
            'losses' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
            'goal_difference' => 0,
            'points' => 0,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $scoreBoards = ScoreBoard::with('team')->orderByDesc('points')->orderByDesc('goal_difference')->get();
        $predictions = $this->championshipPredictionService->getPredictions(0);

        return response()->json([
            'current_week' => 0,
            'played_matches' => [],
            'league_table' => ScoreBoardResource::collection($scoreBoards),
            'championship_predictions' => $predictions,
            'message' => 'Simulation data has been reset.'
        ]);
    }

    public function getCurrentState(int $currentWeek = 0)
    {
        if ($currentWeek === 0) {
            $currentWeek = Fixture::where('is_played', true)->max('week') ?? 0;
        }

        $scoreBoards = ScoreBoard::with('team')->orderByDesc('points')->orderByDesc('goal_difference')->get();
        $fixtures = Fixture::with(['homeTeam', 'awayTeam'])->orderBy('week')->get();

        $playedGames = Game::whereHas('fixture', function ($query) {
                                $query->where('is_played', true);
                            })
                            ->with(['fixture', 'fixture.homeTeam', 'fixture.awayTeam'])
                            ->orderByDesc('updated_at')
                            ->get();


        $predictions = $this->championshipPredictionService->getPredictions($currentWeek);


        return response()->json([
            'current_week' => $currentWeek,
            'all_fixtures' => FixtureResource::collection($fixtures),
            'played_matches' => GameResource::collection($playedGames),
            'league_table' => ScoreBoardResource::collection($scoreBoards),
            'championship_predictions' => $predictions,
        ]);
    }
}