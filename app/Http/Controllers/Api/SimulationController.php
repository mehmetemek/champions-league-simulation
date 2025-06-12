<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Models\ScoreBoard;
use App\Models\Team;
use App\Models\Game; // Game modelini kullan
use App\Http\Resources\FixtureResource;
use App\Http\Resources\ScoreBoardResource;
use App\Http\Resources\GameResource; // GameResource'ı kullan
use App\Services\MatchSimulatorService;
use App\Services\ScoreBoardUpdaterService;
use App\Services\ChampionshipPredictionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Schema facade'ı kullan (truncate için)
use Illuminate\Validation\Rule; // Validasyon kuralları için

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

    /**
     * Belirli bir haftadaki maçları simüle eder.
     *
     * @param Request $request
     * @param int $week
     * @return \Illuminate\Http\JsonResponse
     */
    public function playWeek(Request $request, int $week)
    {
        // Haftanın geçerli olup olmadığını kontrol et
        $maxWeek = Fixture::max('week') ?? 0;
        if ($week <= 0 || $week > $maxWeek) {
            return response()->json(['message' => 'Geçersiz hafta numarası.'], 400);
        }

        // O hafta oynanmamış fikstürleri çek
        $fixturesToPlay = Fixture::where('week', $week)
                                  ->where('is_played', false)
                                  ->with(['homeTeam', 'awayTeam']) // Eager loading ile takım bilgilerini de çek
                                  ->get();

        if ($fixturesToPlay->isEmpty()) {
            return response()->json(['message' => 'Hafta ' . $week . ' için oynanmamış maç bulunamadı.'], 200);
        }

        $playedGames = []; // Değişken adı "playedMatches" yerine "playedGames"
        foreach ($fixturesToPlay as $fixture) {
            $game = $this->matchSimulatorService->simulateMatch($fixture); // Game modelini döndürür
            $this->scoreBoardUpdaterService->updateScoreBoards($game);
            $playedGames[] = $game;
        }

        // Güncel lig tablosunu ve şampiyonluk tahminlerini al
        $scoreBoards = ScoreBoard::with('team')->orderByDesc('points')->orderByDesc('goal_difference')->get();
        $predictions = $this->championshipPredictionService->getPredictions($week);

        return response()->json([
            'current_week' => $week,
            'played_matches' => GameResource::collection($playedGames), // GameResource kullan
            'league_table' => ScoreBoardResource::collection($scoreBoards),
            'championship_predictions' => $predictions,
            'message' => 'Hafta ' . $week . ' başarıyla simüle edildi.'
        ]);
    }

    public function playAllWeeks()
    {
        $maxWeek = Fixture::max('week') ?? 0;
        $currentPlayedWeek = Fixture::where('is_played', true)->max('week') ?? 0;

        if ($currentPlayedWeek >= $maxWeek) {
            return response()->json(['message' => 'Tüm maçlar zaten oynanmış.'], 200);
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
            'message' => 'Tüm oynanmamış haftalar başarıyla simüle edildi.'
        ]);
    }


    public function resetData()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Game::truncate();
        Fixture::query()->update(['is_played' => false]);

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
            'message' => 'Simülasyon verileri sıfırlandı.'
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
                            ->orderBy('played_at')
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