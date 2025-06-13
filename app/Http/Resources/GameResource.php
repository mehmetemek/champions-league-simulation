<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fixture_id' => $this->fixture_id,
            'home_team_id' => $this->fixture->home_team_id,
            'home_team_name' => $this->fixture->homeTeam->name,
            'away_team_id' => $this->fixture->away_team_id,
            'away_team_name' => $this->fixture->awayTeam->name,
            'home_score' => $this->home_score,
            'away_score' => $this->away_score,
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'home_shoot_count' => $this->home_shoot_count,
            'away_shoot_count' => $this->away_shoot_count,
        ];
    }
}