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
        $this->client = new Client();
        $this->apiKey = Config::get('faceit.api_key');
    }

    /**
     * @param string $nickname
     * @return array|null
     */
    public function getPlayerByNickname(string $nickname): ?array
    {
        return $this->makeRequest('GET', '/players', [
            'query' => [
                'nickname' => $nickname,
            ]
        ]);
    }

    /**
     * @param string $playerId
     * @return array|null
     */
    public function getPlayerById(string $playerId): ?array
    {
        return $this->makeRequest('GET', "/players/{$playerId}");
    }

    /**
     * @param string $playerId
     * @param string $gameId
     * @return array|null
     */
    public function getPlayerStats(string $playerId, string $gameId = 'cs2'): ?array
    {
        return $this->makeRequest('GET', "/players/{$playerId}/stats/{$gameId}");
    }

    /**
     * @param string $playerId
     * @param int $limit
     * @param int $offset
     * @param string|null $gameId
     * @return array|null
     */
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
            'query' => $query
        ]);
    }

    /**
     * @param string $matchId
     * @return array|null
     */
    public function getMatchDetails(string $matchId): ?array
    {
        return $this->makeRequest('GET', "/matches/{$matchId}");
    }

    /**
     * @param string $matchId
     * @return array|null
     */
    public function getMatchStats(string $matchId): ?array
    {
        return $this->makeRequest('GET', "/matches/{$matchId}/stats");
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $options
     * @return array|null
     */
    private function makeRequest(string $method, string $endpoint, array $options = []): ?array
    {
        try {
            if (!isset($options['headers'])) {
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
