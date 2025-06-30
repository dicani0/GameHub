<?php

namespace App\Actions\Faceit;

use App\Services\FaceitService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class GetPlayerHistoryAction
{
    protected FaceitService $faceitService;

    public function __construct(FaceitService $faceitService)
    {
        $this->faceitService = $faceitService;
    }

    /**
     * @param string $playerId
     * @return array|null
     */
    public function execute(string $playerId): ?array
    {
        return Cache::remember('faceit_player_history_' . $playerId, 60 * 60, function () use ($playerId) {
            return $this->getPlayerHistory($playerId);
        });
    }

    /**
     * @param string $playerId
     * @return array|null
     */
    private function getPlayerHistory(string $playerId): ?array
    {
        $history = $this->faceitService->getPlayerHistory($playerId, 20, 0, 'cs2');
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