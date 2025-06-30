<?php

namespace App\Actions\Faceit;

use App\Services\FaceitService;

class GetMatchDetailsAction
{
    protected FaceitService $faceitService;

    public function __construct(FaceitService $faceitService)
    {
        $this->faceitService = $faceitService;
    }

    /**
     * @param string $matchId
     * @param bool $includeStats
     * @return array|null
     */
    public function execute(string $matchId, bool $includeStats = false): ?array
    {
        $matchDetails = $this->getMatchDetails($matchId);

        if (!$matchDetails) {
            return null;
        }

        $result = [
            'match' => $matchDetails,
        ];

        if ($includeStats) {
            $result['stats'] = $this->getMatchStats($matchId);
        }

        return $result;
    }

    /**
     * @param string $matchId
     * @return array|null
     */
    private function getMatchDetails(string $matchId): ?array
    {
        return $this->faceitService->getMatchDetails($matchId);
    }

    /**
     * @param string $matchId
     * @return array|null
     */
    private function getMatchStats(string $matchId): ?array
    {
        return $this->faceitService->getMatchStats($matchId);
    }
}
