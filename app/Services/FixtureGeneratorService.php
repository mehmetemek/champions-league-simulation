<?php

namespace App\Services;

use App\Models\Team;
use App\Models\Fixture;
use Illuminate\Support\Facades\DB;

class FixtureGeneratorService
{
    /**
     * Belirtilen takımlar için tüm lig fikstürlerini oluşturur.
     * Her takım diğer her takımla hem evinde hem deplasmanda oynar.
     *
     * @return bool
     */
    public function generate(): bool
    {
        $teams = Team::all();
        $teamsCount = $teams->count();

        // En az 2 takım veya çift sayıda takım olmalı
        if ($teamsCount < 2 || $teamsCount % 2 != 0) {
            return false;
        }

        $teamSeeder = new \Database\Seeders\TeamSeeder();
        $teamSeeder->run();

        // TeamSeeder'ı çağırmak yerine doğrudan takım ID'lerini kullanın
        $teamIds = $teams->pluck('id')->toArray();

        $fixtures = [];
        $totalWeeks = ($teamsCount - 1) * 2; // Her takımla ikişer kez oynanır

        // Round-robin fikstür algoritması (her takım diğerini bir kez evde, bir kez deplasmanda oynar)
        for ($week = 1; $week <= $totalWeeks; $week++) {
            $currentRoundFixtures = [];
            $teamsInRound = $teamIds;

            for ($i = 0; $i < $teamsCount / 2; $i++) {
                $homeTeamId = $teamsInRound[$i];
                $awayTeamId = $teamsInRound[$teamsCount - 1 - $i];

                $currentRoundFixtures[] = [
                    'week' => $week,
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                ];
            }

            $fixtures = array_merge($fixtures, $currentRoundFixtures);

            // Takımların sıralamasını bir sonraki hafta için döndür
            $firstTeam = array_shift($teamIds); // İlk takımı çıkar
            $lastTeam = array_pop($teamIds);    // Son takımı çıkar
            array_unshift($teamIds, $lastTeam); // Son takımı başa ekle
            array_splice($teamIds, 1, 0, [$firstTeam]); // Çıkarılan ilk takımı ikinci sıraya ekle (sabit tutulan ilk takım hariç)
        }

        Fixture::insert($fixtures);

        return true;
    }
}