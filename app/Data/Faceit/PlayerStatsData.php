<?php

namespace App\Data\Faceit;

use Spatie\LaravelData\Data;

class PlayerStatsData extends Data
{
    public function __construct(
        public string $player_id,
        public string $nickname,
        public array $player_stats,
    ) {}
}
