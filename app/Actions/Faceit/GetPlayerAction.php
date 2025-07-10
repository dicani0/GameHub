<?php

namespace App\Actions\Faceit;

use App\Services\FaceitService;
use Illuminate\Support\Facades\Cache;

class GetPlayerAction
{
    protected FaceitService $faceitService;

    public function __construct(FaceitService $faceitService)
    {
        $this->faceitService = $faceitService;
    }

    public function execute(string $nickname, bool $includeStats = false, bool $includeHistory = false): ?array
    {
        return Cache::remember('faceit_player_'.$nickname, 1, function () use ($nickname
        ) {
            return $this->getPlayerByNickname($nickname);

        });
    }

    private function getPlayerByNickname(string $nickname): ?array
    {
        return $this->faceitService->getPlayerByNickname($nickname);
    }
}
