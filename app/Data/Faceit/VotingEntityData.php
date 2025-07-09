<?php

namespace App\Data\Faceit;

use Spatie\LaravelData\Data;

class VotingEntityData extends Data
{
    public function __construct(
        public string $guid,
        public string $image_lg,
        public string $image_sm,
        public string $name,
        public string $class_name,
        public string $game_map_id,
    ) {}
}