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
                $matchStats = $this->sortPlayersByADR($matchStats);
                $statsData = MatchStatsData::from($matchStats);
            }
        }

        $steamProfiles = Optional::create();
        if ($includeSteamProfiles) {
            $playerIdToSteamIdMap = $this->extractPlayerIdToSteamIdMapping($matchDetails);
            $steamIds = array_values($playerIdToSteamIdMap);
            if (!empty($steamIds)) {
                $steamProfilesData = Cache::remember("steam.profiles." . implode(',', $steamIds), now()->addMinutes(10), function () use ($steamIds) {
                    return $this->getSteamProfilesAction->execute($steamIds);
                });

                $steamProfiles = collect();
                foreach ($playerIdToSteamIdMap as $faceitPlayerId => $steamId) {
                    if ($steamProfilesData->has($steamId)) {
                        $steamProfiles->put($faceitPlayerId, $steamProfilesData->get($steamId));
                    }
                }
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

    private function extractPlayerIdToSteamIdMapping(array $matchDetails): array
    {
        $mapping = [];

        if (isset($matchDetails['teams'])) {
            foreach ($matchDetails['teams'] as $team) {
                if (isset($team['roster'])) {
                    foreach ($team['roster'] as $player) {
                        if (isset($player['player_id'], $player['game_player_id'])) {
                            $mapping[$player['player_id']] = $player['game_player_id'];
                        }
                    }
                }
            }
        }

        return $mapping;
    }

    private function sortPlayersByADR(array $matchStats): array
    {
        if (!isset($matchStats['rounds'])) {
            return $matchStats;
        }

        foreach ($matchStats['rounds'] as &$round) {
            if (!isset($round['teams'])) {
                continue;
            }

            foreach ($round['teams'] as &$team) {
                if (!isset($team['players'])) {
                    continue;
                }

                usort($team['players'], function ($a, $b) {
                    $adrA = $a['player_stats']['ADR'] ?? 0;
                    $adrB = $b['player_stats']['ADR'] ?? 0;
                    return $adrB <=> $adrA;
                });
            }
        }

        return $matchStats;
    }
}
