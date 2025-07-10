<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class FaceitService
{
    private Client $client;

    private string $apiKey;

    private string $baseUrl = 'https://open.faceit.com/data/v4';

    public function __construct()
    {
        $this->client = new Client;
        $this->apiKey = Config::get('faceit.api_key');
    }

    public function getPlayerByNickname(string $nickname): ?array
    {
        return $this->makeRequest('GET', '/players', [
            'query' => [
                'nickname' => $nickname,
            ],
        ]);
    }

    public function getPlayerById(string $playerId): ?array
    {
        return $this->makeRequest('GET', "/players/{$playerId}");
    }

    public function getPlayerStats(string $playerId, string $gameId = 'cs2'): ?array
    {
        return $this->makeRequest('GET', "/players/{$playerId}/stats/{$gameId}");
    }

    public function getPlayerHistory(string $playerId, int $limit = 20, int $offset = 0, ?string $gameId = null): ?array
    {
        $query = [
            'limit' => $limit,
            'offset' => $offset,
        ];

        if ($gameId) {
            $query['game'] = $gameId;
        }

        return $this->makeRequest('GET', "/players/{$playerId}/history", [
            'query' => $query,
        ]);
    }

    public function getMatchDetails(string $matchId): ?array
    {
        return $this->makeRequest('GET', "/matches/{$matchId}");
    }

    public function getMatchStats(string $matchId): ?array
    {
        return $this->makeRequest('GET', "/matches/{$matchId}/stats");
    }

    private function makeRequest(string $method, string $endpoint, array $options = []): ?array
    {
        try {
            if (! isset($options['headers'])) {
                $options['headers'] = [];
            }

            $options['headers']['Authorization'] = "Bearer {$this->apiKey}";

            $response = $this->client->request($method, "{$this->baseUrl}{$endpoint}", $options);

            $contents = $response->getBody()->getContents();

            return json_decode($contents, true);
        } catch (GuzzleException $e) {
            Log::error("Faceit API error: {$e->getMessage()}", [
                'endpoint' => $endpoint,
                'options' => $options,
                'code' => $e->getCode(),
            ]);

            return null;
        }
    }
}
