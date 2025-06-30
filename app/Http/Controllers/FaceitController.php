<?php

namespace App\Http\Controllers;

use App\Actions\Faceit\GetMatchDetailsAction;
use App\Actions\Faceit\GetPlayerBasicAction;
use App\Actions\Faceit\GetPlayerStatsAction;
use App\Actions\Faceit\GetPlayerHistoryAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class FaceitController extends Controller
{
    protected GetPlayerBasicAction $getPlayerBasicAction;
    protected GetPlayerStatsAction $getPlayerStatsAction;
    protected GetPlayerHistoryAction $getPlayerHistoryAction;
    protected GetMatchDetailsAction $getMatchDetailsAction;

    public function __construct(
        GetPlayerBasicAction $getPlayerBasicAction,
        GetPlayerStatsAction $getPlayerStatsAction,
        GetPlayerHistoryAction $getPlayerHistoryAction,
        GetMatchDetailsAction $getMatchDetailsAction
    ) {
        $this->getPlayerBasicAction = $getPlayerBasicAction;
        $this->getPlayerStatsAction = $getPlayerStatsAction;
        $this->getPlayerHistoryAction = $getPlayerHistoryAction;
        $this->getMatchDetailsAction = $getMatchDetailsAction;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getPlayerResults(Request $request): Response
    {
        $nickname = $request->input('nickname');
        if (!$nickname) {
            return Inertia::render('counterstrike/FaceitSearch', [
                'error' => 'Nickname is required'
            ]);
        }

        try {
            $player = $this->getPlayerBasicAction->execute($nickname);
            
            if (!$player) {
                return Inertia::render('counterstrike/FaceitSearch', [
                    'error' => 'Player not found'
                ]);
            }
            $includeStats = $request->boolean('include_stats', false);
            $includeHistory = $request->boolean('include_history', false);
            return Inertia::render('counterstrike/FaceitSearch', [
                'player' => $player,
                'stats' => $includeStats ? Inertia::defer(fn () => $this->getPlayerStatsAction->execute($player['player_id']), 'stats') : null,
                'history' => $includeHistory ? Inertia::defer(fn () => $this->getPlayerHistoryAction->execute
                ($player['player_id']), 'history') : null,
                'includeStats' => $includeStats,
                'includeHistory' => $includeHistory,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching player data: ' . $e->getMessage());

            return Inertia::render('counterstrike/FaceitSearch', [
                'error' => 'Failed to fetch player data'
            ]);
        }
    }


    /**
     * @param Request $request
     * @param string $matchId
     * @return JsonResponse
     */
    public function getMatchDetails(Request $request, string $matchId): JsonResponse
    {
        $includeStats = $request->boolean('include_stats', false);

        try {
            $match = $this->getMatchDetailsAction->execute($matchId, $includeStats);

            if (!$match) {
                return response()->json([
                    'error' => 'Match not found',
                ], 404);
            }

            return response()->json($match);
        } catch (\Exception $e) {
            Log::error('Error fetching match data: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to fetch match data',
            ], 500);
        }
    }
}
