<?php

namespace PartyGames\GameApi;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use PartyGames\GameApi\Models\Game;
use PartyGames\GameApi\Models\GameInstances;
use PartyGames\GameApi\Models\PlayerInstances;
use PartyGames\GameApi\Models\UserStats;
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

        switch ($status) {
            case 'completed':
                self::updateUserStatsWithPoints($gameToken);
                self::closePlayerInstances($gameToken);
                break;
            default:
                break;
        }

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

    public static function getPlayerInstance($gameInstanceId, $userId)
    {
        $playerInstance = PlayerInstances::where('game_instance_id', $gameInstanceId)->where('user_id', $userId)->first();
        if(!$playerInstance){
            return ['status' => false, 'playerInstance' => NULL, 'message' => 'Could not find Player Instance'];
        }

        return ['status' => true, 'playerInstance' => $playerInstance, 'message' => 'Player Instance found'];
    }

    public static function updatePlayerInstanceScore($playerInstanceId, $score)
    {
        PlayerInstances::where('id', $playerInstanceId)->update([
            'points' => $score,
        ]);
    }

    public static function updatePlayerInstanceRemoteData($playerInstanceId, $remoteData)
    {
        PlayerInstances::where('id', $playerInstanceId)->update([
            'remote_data' => json_encode($remoteData),
        ]);
    }

    public static function getWinners($gameInstanceId)
    {
        $players = PlayerInstances::where('game_instance_id', $gameInstanceId)->orderBy('points', 'desc')->get();

        $winners = [];
        if ($players->where('user_id', '!=', $players->first()->user_id)->count() > 0) {
            foreach ($players->where('user_id', '!=', $players->first()->user_id) as $player) {
                $winners[] = $player->load('user');
            }
        }

        //orders $winners by points
        usort($winners, function ($a, $b) {
            return $a->points <= $b->points;
        });

        $response = [
            'winner' => $players->first()->load('user'),
            'winners' => $winners
        ];

        return ['status' => true, 'response' => $response, 'message' => 'Winners found'];
    }

    public static function addUserStats($userId, $key, $value)
    {
        $userStats = UserStats::where('user_id', $userId)->where('key', $key)->first();
        if ($userStats) {
            $userStats->value = $value + $userStats->value;
            $userStats->save();
        } else {
            $userStats = UserStats::create([
                'user_id' => $userId,
                'key' => $key,
                'value' => $value,
            ]);
        }
        return ['status' => true, 'userStats' => $userStats, 'message' => 'User Stats added'];
    }

    public static function replaceUserStats($userId, $key, $value)
    {
        $userStats = UserStats::where('user_id', $userId)->where('key', $key)->first();
        if ($userStats) {
            $userStats->value = $value;
            $userStats->save();
        } else {
            $userStats = UserStats::create([
                'user_id' => $userId,
                'key' => $key,
                'value' => $value,
            ]);
        }
        return ['status' => true, 'userStats' => $userStats, 'message' => 'User Stats added'];
    }

    public static function getUserStats($userId, $key)
    {
        $userStats = UserStats::where('user_id', $userId)->where('key', $key)->first();
        if(!$userStats){
            return null;
        }
        return $userStats;
    }

    public static function updateUserStatsWithPoints($gameToken)
    {
        $gameInstance = GameInstances::where('token', $gameToken)->where('user_id', Auth::user()->id)->first();
        if (!$gameInstance) {
            return false;
        }
        $playerInstances = PlayerInstances::where('game_instance_id', $gameInstance->id)->get();

        foreach ($playerInstances as $player) {
            self::addUserStats($player->user_id, 'points', $player->points);
        }

        return ['status' => true, 'message' => 'User Stats updated'];
    }

    public static function closePlayerInstances($gameToken)
    {
        $gameInstance = GameInstances::where('token', $gameToken)->where('user_id', Auth::user()->id)->first();
        if (!$gameInstance) {
            return ['status' => false, 'message' => 'Could not find Game Instance'];
        }
        $playerInstances = PlayerInstances::where('game_instance_id', $gameInstance->id)->get();
        foreach ($playerInstances as $player) {
            $player->status = 'completed';
            $player->save();
        }
        return ['status' => true, 'message' => 'Player Instances closed'];
    }

    public static function closePlayerInstance($playerInstanceId)
    {
        $playerInstance = PlayerInstances::where('id', $playerInstanceId)->where('user_id', Auth::user()->id)->first();
        if (!$playerInstance) {
            return ['status' => false, 'message' => 'Could not find Player Instance'];
        }

        $playerInstance->status = 'completed';
        $playerInstance->save();

        return ['status' => true, 'message' => 'Player Instance closed'];
    }


    public static function getUserActivePlayerInstances($userId)
    {
        if(Auth::user()->id != $userId){
            return [];
        }
        $playerInstances = PlayerInstances::where('user_id', $userId)->where('status', '!=', 'completed')->get();
        return $playerInstances;
    }

    public static function getAllGames($isActive = 0)
    {
        $returnObject = [];

        $allOpenGameInstances = GameInstances::where('status', '!=', 'completed')->get();
        foreach ($allOpenGameInstances as $game) {
            $returnObject[] = [
                'game' => $game->toArray(),
                'players' => $game->playerInstances->load('user')->toArray()
            ];
        }

        return ['games' => $returnObject];
    }
}