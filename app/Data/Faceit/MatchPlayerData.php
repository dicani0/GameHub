<?php

namespace App\Data\Faceit;

use Spatie\LaravelData\Data;

class MatchPlayerData extends Data
{
    public function __construct(
        public string $player_id,
        public string $nickname,
        public string $avatar,
        public string $membership,
        public string $game_player_id,
        public string $game_player_name,
        public int $game_skill_level,
        public bool $anticheat_required,
    ) {}
}