<?php

namespace App\Data\Faceit;

use Spatie\LaravelData\Data;

class MatchResultsData extends Data
{
    public function __construct(
        public string $winner,
        public array $score,
    ) {}
}