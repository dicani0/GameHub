<?php

namespace App\Data\Faceit;

use Spatie\LaravelData\Data;

class VotingData extends Data
{
    public function __construct(
        public array $location,
        public array $map,
        public array $voted_entity_types,
    ) {}
}
