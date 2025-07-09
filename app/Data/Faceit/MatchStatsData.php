<?php

namespace App\Data\Faceit;

use Spatie\LaravelData\Data;

class MatchStatsData extends Data
{
    public function __construct(
        public array $rounds,
    ) {}
}