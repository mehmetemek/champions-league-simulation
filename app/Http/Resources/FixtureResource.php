<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FixtureResource extends JsonResource
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
            'week' => $this->week,
            'home_team_id' => $this->home_team_id,
            'home_team_name' => $this->homeTeam->name,
            'away_team_id' => $this->away_team_id,
            'away_team_name' => $this->awayTeam->name,
            'is_played' => $this->is_played
        ];
    }
}