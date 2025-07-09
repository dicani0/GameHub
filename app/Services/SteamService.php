<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class SteamService
{
    private Client $client;
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = Config::get('steam.api_key');
        $this->baseUrl = Config::get('steam.base_url');
    }

    public function getPlayerSummaries(array $steamIds): ?array
    {
        $steamIdsString = implode(',', $steamIds);

        return $this->makeRequest('GET', '/ISteamUser/GetPlayerSummaries/v0002/', [
            'query' => [
                'key' => $this->apiKey,
                'steamids' => $steamIdsString,
            ]
        ]);
    }

    public function getPlayerSummary(string $steamId): ?array
    {
        $response = $this->getPlayerSummaries([$steamId]);

        if ($response && isset($response['response']['players']) && count($response['response']['players']) > 0) {
            return $response['response']['players'][0];
        }

        return null;
    }

    public function getCSGOStats(string $steamId): ?array
    {
        return $this->makeRequest('GET', '/ISteamUserStats/GetUserStatsForGame/v0002/', [
            'query' => [
                'key' => $this->apiKey,
                'steamid' => $steamId,
                'appid' => '730', // CS:GO App ID
            ]
        ]);
    }

    public function getCS2Stats(string $steamId): ?array
    {
        return $this->makeRequest('GET', '/ISteamUserStats/GetUserStatsForGame/v0002/', [
            'query' => [
                'key' => $this->apiKey,
                'steamid' => $steamId,
                'appid' => '730', // CS2 uses the same App ID as CS:GO
            ]
        ]);
    }

    public function getOwnedGames(string $steamId): ?array
    {
        return $this->makeRequest('GET', '/IPlayerService/GetOwnedGames/v0001/', [
            'query' => [
                'key' => $this->apiKey,
                'steamid' => $steamId,
                'include_appinfo' => 1,
                'include_played_free_games' => 1,
            ]
        ]);
    }

    public function getCS2Playtime(string $steamId): ?int
    {
        $ownedGames = $this->getOwnedGames($steamId);
        if (!$ownedGames || !isset($ownedGames['response']['games'])) {
            return null;
        }

        foreach ($ownedGames['response']['games'] as $game) {
            if ($game['appid'] === 730) {
                return $game['playtime_forever'] ? : null;
            }
        }


        return null;
    }

    public function getCS2PlaytimeForMultiplePlayers(array $steamIds): array
    {
        $playtimes = [];

        foreach ($steamIds as $steamId) {
            $playtimes[$steamId] = $this->getCS2Playtime($steamId);
        }
        return $playtimes;
    }

    private function makeRequest(string $method, string $endpoint, array $options = []): ?array
    {
        try {
            $response = $this->client->request($method, "{$this->baseUrl}{$endpoint}", $options);

            $contents = $response->getBody()->getContents();
            return json_decode($contents, true);
        } catch (GuzzleException $e) {
            Log::error("Steam API error: {$e->getMessage()}", [
                'endpoint' => $endpoint,
                'options' => $options,
                'code' => $e->getCode(),
            ]);

            return null;
        }
    }
}
