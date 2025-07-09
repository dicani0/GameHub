<?php

namespace App\Data\Faceit;

use Spatie\LaravelData\Data;

class TeamStatsData extends Data
{
    public function __construct(
        public float $winProbability,
        public array $skillLevel,
        public int $rating,
    ) {}
}
