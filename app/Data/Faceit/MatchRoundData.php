<?php

namespace App\Data\Faceit;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class MatchRoundData extends Data
{
    public function __construct(
        public string $best_of,
        public ?string $competition_id,
        public string $game_id,
        public string $game_mode,
        public string $match_id,
        public string $match_round,
        public string $played,
        public array $round_stats,
        #[DataCollectionOf(MatchTeamStatsData::class)]
        public DataCollection $teams,
    ) {}
}
