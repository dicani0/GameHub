<?php

namespace App\Data\Faceit;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class MatchTeamStatsData extends Data
{
    public function __construct(
        public string $team_id,
        public bool $premade,
        public array $team_stats,
        #[DataCollectionOf(PlayerStatsData::class)]
        public DataCollection $players,
    ) {}
}
