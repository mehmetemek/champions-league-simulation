<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FixtureGeneratorService;
use App\Http\Resources\FixtureResource;
use App\Models\Fixture;


class FixtureController extends Controller
{
    protected $fixtureGeneratorService;

    public function __construct(FixtureGeneratorService $fixtureGeneratorService)
    {
        $this->fixtureGeneratorService = $fixtureGeneratorService;
    }

    public function generate()
    {
        $generated = $this->fixtureGeneratorService->generate();

        if ($generated) {
            return FixtureResource::collection(Fixture::with(['homeTeam', 'awayTeam'])->orderBy('week')->get());
        }

        return response()->json(['message' => 'Fixtures could not be created. Please make sure there are at least 2 teams.'], 400);
    }

    public function index()
    {
        $fixtures = Fixture::with(['homeTeam', 'awayTeam', 'game'])->orderBy('week')->get();
        return FixtureResource::collection($fixtures);
    }
}