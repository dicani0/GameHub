<?php

namespace App\Data\Steam;

use Spatie\LaravelData\Data;

class SteamProfileData extends Data
{
    public function __construct(
        public string $steamid,
        public string $personaname,
        public string $profileurl,
        public string $avatar,
        public string $avatarmedium,
        public string $avatarfull,
        public int $personastate,
        public int $communityvisibilitystate,
        public ?string $realname = null,
        public ?int $timecreated = null,
        public ?string $loccountrycode = null,
        public ?string $locstatecode = null,
        public ?int $loccityid = null,
        public ?int $cs2_playtime_minutes = null,
        public ?int $level = null,
    ) {}

    public function getCS2PlaytimeHours(): ?float
    {
        if ($this->cs2_playtime_minutes === null) {
            return null;
        }

        return round($this->cs2_playtime_minutes / 60, 1);
    }

    public function getFormattedCS2Playtime(): string
    {
        $hours = $this->getCS2PlaytimeHours();

        if ($hours === null) {
            return 'Private';
        }

        if ($hours < 1) {
            return '<1h';
        }

        return number_format($hours, 1) . 'h';
    }
}
