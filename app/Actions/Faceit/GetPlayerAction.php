<?php

namespace App\Actions\Faceit;

use App\Services\FaceitService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class GetPlayerAction
{
    protected FaceitService $faceitService;

    public function __construct(FaceitService $faceitService)
    {
        $this->faceitService = $faceitService;
    }

    /**
     * @param string $nickname
     * @param bool $includeStats
     * @param bool $includeHistory
     * @return array|null
     */
    public function execute(string $nickname, bool $includeStats = false, bool $includeHistory = false): ?array
    {
        //TODO better cache management, split cache for stats and history, add possibility to force refresh data
        return Cache::remember('faceit_player_' . $nickname, 60 * 60, function () use ($nickname, $includeStats, $includeHistory) {
            $player = $this->getPlayerByNickname($nickname);

            if (!$player) {
                return null;
            }

            $result = [
                'player' => $player,
            ];

            if ($includeStats && isset($player['player_id'])) {
                $result['stats'] = $this->getPlayerStats($player['player_id']);
            }

            if ($includeHistory && isset($player['player_id'])) {
                $result['history'] = $this->getPlayerHistory($player['player_id']);
            }

            return $result;
        });
    }

    /**
     * @param string $nickname
     * @return array|null
     */
    private function getPlayerByNickname(string $nickname): ?array
    {
        return $this->faceitService->getPlayerByNickname($nickname);
    }

    /**
     * @param string $playerId
     * @return array|null
     */
    private function getPlayerStats(string $playerId): ?array
    {
        $stats = $this->faceitService->getPlayerStats($playerId, 'cs2');
        
        if (!$stats || !isset($stats['segments'])) {
            return null;
        }

        foreach ($stats['segments'] as $segment) {
            if ($segment['mode'] === '5v5') {
                return [
                    'elo' => $stats['games']['cs2']['faceit_elo'] ?? null,
                    'skill_level' => $stats['games']['cs2']['skill_level'] ?? null,
                    'kd_ratio' => $segment['stats']['k/d'] ?? null,
                    'avg_kills' => $segment['stats']['Average Kills'] ?? null,
                    'win_rate' => $segment['stats']['Win Rate %'] ?? null,
                    'matches' => $segment['stats']['Matches'] ?? null,
                    'avg_headshots' => $segment['stats']['Average Headshots %'] ?? null,
                ];
            }
        }

        return null;
    }

    /**
     * @param string $playerId
     * @return array|null
     */
    private function getPlayerHistory(string $playerId): ?array
    {
        $history = $this->faceitService->getPlayerHistory($playerId, 10, 0, 'cs2');
        if (!$history || !isset($history['items'])) {
            return null;
        }
        $history = $history['items'];
        return $this->mapMatchDetails($history, $playerId);
    }

    /**
     * @param mixed  $history
     * @param string $playerId
     *
     * @return array
     */
    private function mapMatchDetails(mixed $history, string $playerId): array
    {
        return Arr::map($history, function (array $item) use ($playerId) {
            $faction1 = $item['teams']['faction1']['players'];
            $faction2 = $item['teams']['faction2']['players'];

            $faction1 = Arr::first($faction1, static function (array $player) use ($playerId) {
                return $player['player_id'] === $playerId;
            });
            $faction2 = Arr::first($faction2, static function (array $player) use ($playerId) {
                return $player['player_id'] === $playerId;
            });

            $item['match_details'] = $this->faceitService->getMatchDetails($item['match_id']);
            $item['player_faction'] = $faction1 ? 'faction1' : ($faction2 ? 'faction2' : null);
            $item['results']['victory'] = $item['results']['winner'] === $item['player_faction'];

            return $item;
        });
    }
}