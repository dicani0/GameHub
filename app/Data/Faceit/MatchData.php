<?php

namespace App\Data\Faceit;

use Spatie\LaravelData\Data;

class MatchData extends Data
{
    public function __construct(
        public string $match_id,
        public int $version,
        public string $game,
        public string $region,
        public string $competition_id,
        public string $competition_type,
        public string $competition_name,
        public string $organizer_id,
        public array $teams,
        public array $voting,
        public bool $calculate_elo,
        public int $configured_at,
        public int $started_at,
        public int $finished_at,
        public array $demo_url,
        public string $chat_room_id,
        public int $best_of,
        public array $results,
        public array $detailed_results,
        public string $status,
        public string $faceit_url,
    ) {}
}