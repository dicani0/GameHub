<?php

namespace App\Data\Faceit;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class MatchDetailsData extends Data
{
    public function __construct(
        public MatchData $match,
        public MatchStatsData|Optional $stats,
        public Collection|Optional $steamProfiles,
    ) {}
}
