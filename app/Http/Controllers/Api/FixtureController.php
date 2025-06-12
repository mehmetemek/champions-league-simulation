<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team; // Team modelini import edin
use App\Services\FixtureGeneratorService; // Servisi import edin
use App\Http\Resources\FixtureResource; // FixtureResource'u import edin
use App\Models\Fixture; // Fixture modelini import edin
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema; // Schema facade'ı import edin (truncate için)


class FixtureController extends Controller
{
    protected $fixtureGeneratorService;

    public function __construct(FixtureGeneratorService $fixtureGeneratorService)
    {
        $this->fixtureGeneratorService = $fixtureGeneratorService;
    }

    /**
     * Takımlar için fikstürleri oluşturur.
     */
    public function generate()
    {
        $generated = $this->fixtureGeneratorService->generate();

        if ($generated) {
            return FixtureResource::collection(Fixture::with(['homeTeam', 'awayTeam'])->orderBy('week')->get());
        }

        return response()->json(['message' => 'Fikstürler oluşturulamadı, lütfen en az 2 takım olduğundan emin olun.'], 400);
    }

    /**
     * Tüm fikstürleri haftalarına göre listele.
     */
    public function index()
    {
        $fixtures = Fixture::with(['homeTeam', 'awayTeam', 'game'])->orderBy('week')->get();
        return FixtureResource::collection($fixtures);
    }
}