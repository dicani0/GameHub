<?php

namespace App\Actions\Faceit;

use App\Data\Faceit\MatchDetailsData;
use App\Data\Faceit\MatchData;
use App\Data\Faceit\MatchStatsData;
use App\Services\FaceitService;
use Spatie\LaravelData\Optional;

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
     * @return MatchDetailsData|null
     */
    public function execute(string $matchId, bool $includeStats = false): ?MatchDetailsData
    {
        $matchDetails = $this->getMatchDetails($matchId);

        if (!$matchDetails) {
            return null;
        }

        $matchData = MatchData::from($matchDetails);
        
        $statsData = Optional::create();
        if ($includeStats) {
            $matchStats = $this->getMatchStats($matchId);
            if ($matchStats) {
                $statsData = MatchStatsData::from($matchStats);
            }
        }

        return new MatchDetailsData(
            match: $matchData,
            stats: $statsData
        );
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
