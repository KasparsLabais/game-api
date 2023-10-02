<?php

namespace PartyGames\GameApi;

use Illuminate\Support\Str;
use PartyGames\GameApi\Models\Game;
use PartyGames\GameApi\Models\GameInstances;
use PartyGames\GameApi\Models\PlayerInstances;
use Illuminate\Support\Facades\Auth;

class GameApi
{

    protected $name;
    protected $dummy = 0;

    public function __construct()
    {
    }

    public static function registerGame($token, $details = [])
    {
        if (!isset($details['start_point_url'])) {
            return ['status' => false, 'message' => 'Start point url is required.'];
        }

        Game::where('token', $token)->update([
            'version' => $details['version'],
            'start_point_url' => $details['start_point_url'],
            'description' => $details['description'],
            'creators_name' => $details['creators_name'],
            'creators_url' => $details['creators_url'],
            'is_enabled' => 1,
        ]);

        return ['status' => true, 'message' => 'Game is registered.'];
    }

    public static function createGameInstance($token, $title, $remoteData = NULL)
    {
        $game = Game::where('token', '=', $token)->first();

        if (!$game) {
            return ['status' => false, 'gameInstance' => NULL, 'message' => 'Could not find Game'];
        }

        if (!Auth::check()) {
            return ['status' => false, 'gameInstance' => NULL, 'message' => 'You need to be logged in to create a game instance.'];
        }

        $newGameInstance = GameInstances::create([
            'title' => $title,
            'game_id' => $game['id'],
            'user_id' => Auth::user()->id,
            'status' => 'created',
            'token' => GameAPi::createToken(15),
            'remote_data' => json_encode($remoteData)
        ]);

        if (!$newGameInstance) {
            return ['status' => false, 'gameInstance' => NULL, 'message' => 'Failed to create game instance.'];
        }

        return ['status' => true, 'gameInstance' => $newGameInstance, 'message' => 'Woop Woop Game Instance is created!'];
    }

    public static function getGameInstance($gameToken = null)
    {
        $gameInstance = GameInstances::where('token', $gameToken)->first();
        $gameInstance->load('user')->load('game')->load('playerInstances');

        if (!$gameInstance) {
            return ['status' => false, 'gameInstance' => NULL, 'message' => 'Could not find Game Instance'];
        }

        return ['status' => true, 'gameInstance' => $gameInstance, 'message' => 'Game Instance found'];
    }

    public static function changeGameInstanceStatus($gameToken, $status = 'created')
    {
        GameInstances::where('token', $gameToken)->where('user_id', Auth::user()->id)->update([
            'status' => $status,
        ]);
        $gameInstance = GameInstances::where('token', $gameToken)->first();
        return ['status' => true, 'gameInstance' => $gameInstance, 'message' => 'Game Instance status changed'];
    }

    public static function updateGameInstanceRemoteData($gameToken, $remoteData)
    {
        GameInstances::where('token', $gameToken)->where('user_id', Auth::user()->id)->update([
            'remote_data' => $remoteData,
        ]);
    }

    public static function closeGameInstance()
    {

    }

    public static function joinGameInstance($userId, $gameToken = null)
    {
        $gameInstance = GameInstances::where('token', $gameToken)->first();

        if (!$gameInstance) {
            return ['status' => false, 'gameInstance' => NULL, 'playerInstance' => NULL, 'message' => 'Could not find Game Instance'];
        }

        $playerInstance = PlayerInstances::firstOrCreate([
            'user_id' => $userId,
            'game_instance_id' => $gameInstance->id,
            'points' => 0,
            'status' => 'joined',
            'remote_data' => NULL,
        ]);

        if (!$playerInstance) {
            return ['status' => false, 'gameInstance' => NULL, 'playerInstance' => NULL, 'message' => 'Could not join Game Instance'];
        }

        return ['status' => true, 'gameInstance' => $gameInstance, 'playerInstance' => $playerInstance, 'message' => 'Joined Game Instance'];
    }

    public function leaveGameInstance()
    {

    }

    public function hello()
    {
        return "Woobly Jumbli {$this->name}";
    }

    public static function createToken($length = 120)
    {
        //TODO: Check if token already exists and is active
        return Str::random(15);
    }
}