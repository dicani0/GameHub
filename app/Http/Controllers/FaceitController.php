<?php

namespace App\Http\Controllers;

use App\Actions\Faceit\GetMatchDetailsAction;
use App\Actions\Faceit\GetPlayerAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FaceitController extends Controller
{
    protected GetPlayerAction $getPlayerAction;
    protected GetMatchDetailsAction $getMatchDetailsAction;

    public function __construct(
        GetPlayerAction $getPlayerAction,
        GetMatchDetailsAction $getMatchDetailsAction
    ) {
        $this->getPlayerAction = $getPlayerAction;
        $this->getMatchDetailsAction = $getMatchDetailsAction;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getPlayer(Request $request): JsonResponse
    {
        $nickname = $request->input('nickname');
        
        if (!$nickname) {
            return response()->json([
                'error' => 'Nickname is required',
            ], 400);
        }
        
        $includeStats = $request->boolean('include_stats', false);
        $includeHistory = $request->boolean('include_history', false);
        
        try {
            $player = $this->getPlayerAction->execute($nickname, $includeStats, $includeHistory);
            if (!$player) {
                return response()->json([
                    'error' => 'Player not found',
                ], 404);
            }
            
            return response()->json($player);
        } catch (\Exception $e) {
            Log::error('Error fetching player data: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to fetch player data',
            ], 500);
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