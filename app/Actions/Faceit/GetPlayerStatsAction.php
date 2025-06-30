<?php

namespace App\Actions\Faceit;

use App\Services\FaceitService;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class GetPlayerStatsAction
{
    private array $numericStats = [
        'Kills',
        'Deaths',
        'Assists',
        'ADR',
        'Headshots',
        'Headshots %',
        'MVPs',
        'Double Kills',
        'Triple Kills',
        'Quadro Kills',
        'Penta Kills',
    ];

    protected FaceitService $faceitService;

    public function __construct(FaceitService $faceitService)
    {
        $this->faceitService = $faceitService;
    }

    /**
     * @param string $playerId
     *
     * @return array|null
     */
    public function execute(string $playerId): ?array
    {
        return Cache::remember('faceit_player_stats_' . $playerId, 1 * 1, function () use ($playerId) {
            return $this->getPlayerStats($playerId);
        });
    }

    private function getPlayerStats(string $playerId): ?array
    {
        $stats = $this->faceitService->getPlayerStats($playerId, 'cs2');

        if (!$stats || !isset($stats['lifetime']) || empty($stats['lifetime'])) {
            return null;
        }
        $count = $stats['lifetime']['Matches'];
        return Arr::mapWithKeys($stats['lifetime'], function ($stat, $key) use ($count) {
           return [Str::of($key)->lower()->snake()->toString() => $stat];
        });
    }
}