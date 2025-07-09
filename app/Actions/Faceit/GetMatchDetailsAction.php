<?php

namespace App\Actions\Faceit;

use App\Actions\Steam\GetSteamProfilesAction;
use App\Data\Faceit\MatchDetailsData;
use App\Data\Faceit\MatchData;
use App\Data\Faceit\MatchStatsData;
use App\Services\FaceitService;
use Illuminate\Support\Facades\Cache;
use Spatie\LaravelData\Optional;

class GetMatchDetailsAction
{
    protected FaceitService $faceitService;
    protected GetSteamProfilesAction $getSteamProfilesAction;

    public function __construct(FaceitService $faceitService, GetSteamProfilesAction $getSteamProfilesAction)
    {
        $this->faceitService = $faceitService;
        $this->getSteamProfilesAction = $getSteamProfilesAction;
    }


    public function execute(string $matchId, bool $includeStats = false, bool $includeSteamProfiles = false): ?MatchDetailsData
    {
        $matchDetails = Cache::remember("faceit.match_details.{$matchId}", now()->addMinutes(10), function () use ($matchId) {
            return $this->getMatchDetails($matchId);
        });

        if (!$matchDetails) {
            return null;
        }

        $matchData = MatchData::from($matchDetails);

        $statsData = Optional::create();
        if ($includeStats) {
            $matchStats = Cache::remember("faceit.match_stats.{$matchId}", now()->addMinutes(10), function () use ($matchId) {
                return $this->getMatchStats($matchId);
            });

            if ($matchStats) {
                $statsData = MatchStatsData::from($matchStats);
            }
        }

        $steamProfiles = Optional::create();
        if ($includeSteamProfiles) {
            $steamIds = $this->extractSteamIdsFromMatch($matchDetails);
            if (!empty($steamIds)) {
                $steamProfiles = Cache::remember("steam.profiles." . implode(',', $steamIds), now()->addMinutes(10), function () use ($steamIds) {
                    return $this->getSteamProfilesAction->execute($steamIds);
                });
            }
        }

        return new MatchDetailsData(
            match: $matchData,
            stats: $statsData,
            steamProfiles: $steamProfiles
        );
    }

    private function getMatchDetails(string $matchId): ?array
    {
        return $this->faceitService->getMatchDetails($matchId);
    }


    private function getMatchStats(string $matchId): ?array
    {
        return $this->faceitService->getMatchStats($matchId);
    }

    private function extractSteamIdsFromMatch(array $matchDetails): array
    {
        $steamIds = [];

        if (isset($matchDetails['teams'])) {
            foreach ($matchDetails['teams'] as $team) {
                if (isset($team['roster'])) {
                    foreach ($team['roster'] as $player) {
                        if (isset($player['game_player_id'])) {
                            $steamIds[] = $player['game_player_id'];
                        }
                    }
                }
            }
        }

        return array_unique($steamIds);
    }
}
