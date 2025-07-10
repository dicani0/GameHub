<?php

use App\Actions\Faceit\GetMatchDetailsAction;
use App\Actions\Faceit\GetPlayerBasicAction;
use App\Actions\Faceit\GetPlayerStatsAction;
use App\Actions\Faceit\GetPlayerHistoryAction;
use App\Data\Faceit\MatchDetailsData;
use App\Data\Faceit\MatchData;
use App\Data\Faceit\MatchStatsData;
use Illuminate\Support\Facades\Log;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\LaravelData\Optional;

beforeEach(function () {
    $this->getPlayerBasicAction = Mockery::mock(GetPlayerBasicAction::class);
    $this->getPlayerStatsAction = Mockery::mock(GetPlayerStatsAction::class);
    $this->getPlayerHistoryAction = Mockery::mock(GetPlayerHistoryAction::class);
    $this->getMatchDetailsAction = Mockery::mock(GetMatchDetailsAction::class);

    $this->app->instance(GetPlayerBasicAction::class, $this->getPlayerBasicAction);
    $this->app->instance(GetPlayerStatsAction::class, $this->getPlayerStatsAction);
    $this->app->instance(GetPlayerHistoryAction::class, $this->getPlayerHistoryAction);
    $this->app->instance(GetMatchDetailsAction::class, $this->getMatchDetailsAction);
});

afterEach(function () {
    Mockery::close();
});

describe('getPlayerResults', function () {
    it('returns error when nickname is missing', function () {
        $response = $this->get('/cs/faceit/results');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('counterstrike/FaceitSearch')
                ->has('error')
                ->where('error', 'Nickname is required')
            );
    });

    it('returns error when player is not found', function () {
        $this->getPlayerBasicAction
            ->shouldReceive('execute')
            ->with('nonexistent')
            ->andReturn(null);

        $response = $this->get('/cs/faceit/results?nickname=nonexistent');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('counterstrike/FaceitSearch')
                ->has('error')
                ->where('error', 'Player not found')
            );
    });

    it('returns player data when found', function () {
        $playerData = [
            'player_id' => 'test-player-id',
            'nickname' => 'testplayer',
            'avatar' => 'https://example.com/avatar.jpg',
            'country' => 'US',
            'skill_level' => 10,
        ];

        $this->getPlayerBasicAction
            ->shouldReceive('execute')
            ->with('testplayer')
            ->andReturn($playerData);

        $response = $this->get('/cs/faceit/results?nickname=testplayer');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('counterstrike/FaceitSearch')
                ->has('player')
                ->where('player', $playerData)
                ->has('includeStats')
                ->where('includeStats', false)
                ->has('includeHistory')
                ->where('includeHistory', false)
                ->missing('error')
            );
    });

    it('includes deferred stats when requested', function () {
        $playerData = [
            'player_id' => 'test-player-id',
            'nickname' => 'testplayer',
        ];

        $this->getPlayerBasicAction
            ->shouldReceive('execute')
            ->with('testplayer')
            ->andReturn($playerData);

        $response = $this->get('/cs/faceit/results?nickname=testplayer&include_stats=1');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('counterstrike/FaceitSearch')
                ->has('player')
                ->where('player', $playerData)
                ->has('includeStats')
                ->where('includeStats', true)
                ->has('includeHistory')
                ->where('includeHistory', false)
            );
    });

    it('includes deferred history when requested', function () {
        $playerData = [
            'player_id' => 'test-player-id',
            'nickname' => 'testplayer',
        ];

        $this->getPlayerBasicAction
            ->shouldReceive('execute')
            ->with('testplayer')
            ->andReturn($playerData);

        $response = $this->get('/cs/faceit/results?nickname=testplayer&include_history=1');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('counterstrike/FaceitSearch')
                ->has('player')
                ->where('player', $playerData)
                ->has('includeStats')
                ->where('includeStats', false)
                ->has('includeHistory')
                ->where('includeHistory', true)
            );
    });

    it('includes both stats and history when requested', function () {
        $playerData = [
            'player_id' => 'test-player-id',
            'nickname' => 'testplayer',
        ];

        $this->getPlayerBasicAction
            ->shouldReceive('execute')
            ->with('testplayer')
            ->andReturn($playerData);

        $response = $this->get('/cs/faceit/results?nickname=testplayer&include_stats=1&include_history=1');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('counterstrike/FaceitSearch')
                ->has('player')
                ->where('player', $playerData)
                ->has('includeStats')
                ->where('includeStats', true)
                ->has('includeHistory')
                ->where('includeHistory', true)
            );
    });

    it('handles exceptions gracefully', function () {
        Log::shouldReceive('error')
            ->once()
            ->with('Error fetching player data: Test exception');

        $this->getPlayerBasicAction
            ->shouldReceive('execute')
            ->with('testplayer')
            ->andThrow(new Exception('Test exception'));

        $response = $this->get('/cs/faceit/results?nickname=testplayer');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('counterstrike/FaceitSearch')
                ->has('error')
                ->where('error', 'Failed to fetch player data')
            );
    });
});

describe('getMatchDetails', function () {
    it('returns match details when found', function () {
        $matchData = new MatchData(
            match_id: 'test-match-id',
            version: 1,
            game: 'cs2',
            region: 'EU',
            competition_id: 'test-comp-id',
            competition_type: 'matchmaking',
            competition_name: 'Test Competition',
            organizer_id: 'faceit',
            teams: [
                'faction1' => [
                    'faction_id' => 'faction1',
                    'leader' => 'leader1',
                    'avatar' => 'https://example.com/avatar1.jpg',
                    'roster' => [],
                    'name' => 'Team 1',
                    'type' => 'premade'
                ],
                'faction2' => [
                    'faction_id' => 'faction2',
                    'leader' => 'leader2',
                    'avatar' => 'https://example.com/avatar2.jpg',
                    'roster' => [],
                    'name' => 'Team 2',
                    'type' => 'premade'
                ]
            ],
            voting: [],
            calculate_elo: true,
            configured_at: 1234567880,
            started_at: 1234567890,
            finished_at: 1234567900,
            demo_url: [],
            chat_room_id: 'test-chat-room',
            best_of: 1,
            results: [],
            detailed_results: [],
            status: 'FINISHED',
            faceit_url: 'https://faceit.com/match/test-match-id'
        );

        $matchDetailsData = new MatchDetailsData(
            match: $matchData,
            stats: Optional::create(),
            steamProfiles: Optional::create()
        );

        $this->getMatchDetailsAction
            ->shouldReceive('execute')
            ->with('test-match-id', false, true)
            ->andReturn($matchDetailsData);

        $response = $this->get('/cs/faceit/match/test-match-id');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'match' => [
                    'match_id',
                    'game',
                    'region',
                    'competition_name',
                    'teams',
                    'status',
                    'started_at',
                    'finished_at'
                ]
            ]);
    });

    it('returns match details with stats when requested', function () {
        $matchData = new MatchData(
            match_id: 'test-match-id',
            version: 1,
            game: 'cs2',
            region: 'EU',
            competition_id: 'test-comp-id',
            competition_type: 'matchmaking',
            competition_name: 'Test Competition',
            organizer_id: 'faceit',
            teams: [
                'faction1' => [
                    'faction_id' => 'faction1',
                    'leader' => 'leader1',
                    'avatar' => 'https://example.com/avatar1.jpg',
                    'roster' => [],
                    'name' => 'Team 1',
                    'type' => 'premade'
                ],
                'faction2' => [
                    'faction_id' => 'faction2',
                    'leader' => 'leader2',
                    'avatar' => 'https://example.com/avatar2.jpg',
                    'roster' => [],
                    'name' => 'Team 2',
                    'type' => 'premade'
                ]
            ],
            voting: [],
            calculate_elo: true,
            configured_at: 1234567880,
            started_at: 1234567890,
            finished_at: 1234567900,
            demo_url: [],
            chat_room_id: 'test-chat-room',
            best_of: 1,
            results: [],
            detailed_results: [],
            status: 'FINISHED',
            faceit_url: 'https://faceit.com/match/test-match-id'
        );

        $statsData = new MatchStatsData(
            rounds: [
                [
                    'match_id' => 'test-match-id',
                    'teams' => [
                        [
                            'team_id' => 'team1',
                            'players' => [
                                [
                                    'player_id' => 'player1',
                                    'nickname' => 'Player1',
                                    'player_stats' => [
                                        'ADR' => 75.5,
                                        'Kills' => 15,
                                        'Deaths' => 10,
                                        'Assists' => 5
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $matchDetailsData = new MatchDetailsData(
            match: $matchData,
            stats: $statsData,
            steamProfiles: Optional::create()
        );

        $this->getMatchDetailsAction
            ->shouldReceive('execute')
            ->with('test-match-id', true, true)
            ->andReturn($matchDetailsData);

        $response = $this->get('/cs/faceit/match/test-match-id?include_stats=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'match' => [
                    'match_id',
                    'game',
                    'region',
                    'competition_name',
                    'teams',
                    'status',
                    'started_at',
                    'finished_at'
                ],
                'stats' => [
                    'rounds' => [
                        '*' => [
                            'teams' => [
                                '*' => [
                                    'players' => [
                                        '*' => [
                                            'player_id',
                                            'nickname',
                                            'player_stats'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    });

    it('returns match details without steam profiles when requested', function () {
        $matchData = new MatchData(
            match_id: 'test-match-id',
            version: 1,
            game: 'cs2',
            region: 'EU',
            competition_id: 'test-comp-id',
            competition_type: 'matchmaking',
            competition_name: 'Test Competition',
            organizer_id: 'faceit',
            teams: [
                'faction1' => [
                    'faction_id' => 'faction1',
                    'leader' => 'leader1',
                    'avatar' => 'https://example.com/avatar1.jpg',
                    'roster' => [],
                    'name' => 'Team 1',
                    'type' => 'premade'
                ],
                'faction2' => [
                    'faction_id' => 'faction2',
                    'leader' => 'leader2',
                    'avatar' => 'https://example.com/avatar2.jpg',
                    'roster' => [],
                    'name' => 'Team 2',
                    'type' => 'premade'
                ]
            ],
            voting: [],
            calculate_elo: true,
            configured_at: 1234567880,
            started_at: 1234567890,
            finished_at: 1234567900,
            demo_url: [],
            chat_room_id: 'test-chat-room',
            best_of: 1,
            results: [],
            detailed_results: [],
            status: 'FINISHED',
            faceit_url: 'https://faceit.com/match/test-match-id'
        );

        $matchDetailsData = new MatchDetailsData(
            match: $matchData,
            stats: Optional::create(),
            steamProfiles: Optional::create()
        );

        $this->getMatchDetailsAction
            ->shouldReceive('execute')
            ->with('test-match-id', false, false)
            ->andReturn($matchDetailsData);

        $response = $this->get('/cs/faceit/match/test-match-id?include_steam_profiles=0');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'match' => [
                    'match_id',
                    'game',
                    'region',
                    'competition_name',
                    'teams',
                    'status',
                    'started_at',
                    'finished_at'
                ]
            ]);
    });

    it('returns 404 when match is not found', function () {
        $this->getMatchDetailsAction
            ->shouldReceive('execute')
            ->with('nonexistent-match-id', false, true)
            ->andReturn(null);

        $response = $this->get('/cs/faceit/match/nonexistent-match-id');

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Match not found'
            ]);
    });

    it('handles exceptions gracefully', function () {
        Log::shouldReceive('error')
            ->once()
            ->with('Error fetching match data: Test exception');

        $this->getMatchDetailsAction
            ->shouldReceive('execute')
            ->with('test-match-id', false, true)
            ->andThrow(new Exception('Test exception'));

        $response = $this->get('/cs/faceit/match/test-match-id');

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'Failed to fetch match data'
            ]);
    });

    it('processes boolean parameters correctly', function () {
        $matchData = new MatchData(
            match_id: 'test-match-id',
            version: 1,
            game: 'cs2',
            region: 'EU',
            competition_id: 'test-comp-id',
            competition_type: 'matchmaking',
            competition_name: 'Test Competition',
            organizer_id: 'faceit',
            teams: [
                'faction1' => [
                    'faction_id' => 'faction1',
                    'leader' => 'leader1',
                    'avatar' => 'https://example.com/avatar1.jpg',
                    'roster' => [],
                    'name' => 'Team 1',
                    'type' => 'premade'
                ],
                'faction2' => [
                    'faction_id' => 'faction2',
                    'leader' => 'leader2',
                    'avatar' => 'https://example.com/avatar2.jpg',
                    'roster' => [],
                    'name' => 'Team 2',
                    'type' => 'premade'
                ]
            ],
            voting: [],
            calculate_elo: true,
            configured_at: 1234567880,
            started_at: 1234567890,
            finished_at: 1234567900,
            demo_url: [],
            chat_room_id: 'test-chat-room',
            best_of: 1,
            results: [],
            detailed_results: [],
            status: 'FINISHED',
            faceit_url: 'https://faceit.com/match/test-match-id'
        );

        $matchDetailsData = new MatchDetailsData(
            match: $matchData,
            stats: Optional::create(),
            steamProfiles: Optional::create()
        );

        $this->getMatchDetailsAction
            ->shouldReceive('execute')
            ->with('test-match-id', true, false)
            ->andReturn($matchDetailsData);

        $response = $this->get('/cs/faceit/match/test-match-id?include_stats=true&include_steam_profiles=false');

        $response->assertStatus(200);
    });
});
