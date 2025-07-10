<?php

namespace App\Actions\Faceit;

use App\Services\FaceitService;
use Illuminate\Support\Facades\Cache;

class GetPlayerBasicAction
{
    protected FaceitService $faceitService;

    public function __construct(FaceitService $faceitService)
    {
        $this->faceitService = $faceitService;
    }

    public function execute(string $nickname): ?array
    {
        return Cache::remember('faceit_player_basic_'.$nickname, 60 * 60, function () use ($nickname) {
            return $this->faceitService->getPlayerByNickname($nickname);
        });
    }
}
