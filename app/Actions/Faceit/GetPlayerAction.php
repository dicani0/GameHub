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
        return Cache::remember('faceit_player_' . $nickname, 1, function () use ($nickname, $includeStats,
            $includeHistory) {
            return $this->getPlayerByNickname($nickname);

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
}