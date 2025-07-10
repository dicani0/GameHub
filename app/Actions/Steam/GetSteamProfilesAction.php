<?php

namespace App\Actions\Steam;

use App\Data\Steam\SteamProfileData;
use App\Services\SteamService;
use Illuminate\Support\Collection;

class GetSteamProfilesAction
{
    protected SteamService $steamService;

    public function __construct(SteamService $steamService)
    {
        $this->steamService = $steamService;
    }

    public function execute(array $steamIds, bool $includePlaytime = true, bool $includeLevel = true): Collection
    {
        if (empty($steamIds)) {
            return collect();
        }

        $response = $this->steamService->getPlayerSummaries($steamIds);

        if (! $response || ! isset($response['response']['players'])) {
            return collect();
        }

        $players = collect($response['response']['players']);

        if ($includePlaytime) {
            $playtimes = $this->steamService->getCS2PlaytimeForMultiplePlayers($steamIds);

            $players = $players->map(function (array $player) use ($playtimes) {
                $player['cs2_playtime_minutes'] = $playtimes[$player['steamid']] ?? null;

                return $player;
            });
        }

        if ($includeLevel) {
            $levels = $this->steamService->getSteamLevelsForMultiplePlayers($steamIds);

            $players = $players->map(function (array $player) use ($levels) {
                $player['level'] = $levels[$player['steamid']] ?? null;

                return $player;
            });
        }

        return $players
            ->map(fn (array $player) => SteamProfileData::from($player))
            ->keyBy('steamid');
    }

    public function getSingle(string $steamId, bool $includePlaytime = true, bool $includeLevel = true): ?SteamProfileData
    {
        return $this->execute([$steamId], $includePlaytime, $includeLevel)->get($steamId);
    }
}
