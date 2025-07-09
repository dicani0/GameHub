<?php

namespace App\Data\Faceit;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class TeamData extends Data
{
    public function __construct(
        public string $faction_id,
        public string $leader,
        public string $avatar,
        #[DataCollectionOf(MatchPlayerData::class)]
        public DataCollection $roster,
        public TeamStatsData $stats,
        public bool $substituted,
        public string $name,
        public string $type,
    ) {}
}