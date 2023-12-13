<?php

namespace PartyGames\GameApi;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use PartyGames\GameApi\Models\Game;
use PartyGames\GameApi\Models\GameInstances;
use PartyGames\GameApi\Models\IconFlairs;
use PartyGames\GameApi\Models\PlayerInstances;
use PartyGames\GameApi\Models\TmpUsers;
use PartyGames\GameApi\Models\User;
use PartyGames\GameApi\Models\UserStats;
use PartyGames\GameApi\Models\GameInstanceSettings;
use PartyGames\GameApi\Models\GameCurrency;

use Illuminate\Support\Facades\Auth;
use PartyGames\TriviaGame\Models\SubmittedAnswers;

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
            'pin' => self::createPin(),
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

    public static function getGameInstanceFromPin($pin = null)
    {
        $gameInstance = GameInstances::where('pin', $pin)->first();

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

    public static function joinGameInstance($userId, $gameToken = null, $playerType = 'player')
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
            'user_type' => $playerType
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

    public static function createPin()
    {
        return rand(1000, 9999);
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

        $first = [];
        $second = [];
        $third = [];
        $winners = [];


        $tmpCounter = 1;
        $tmpUser = [];
        //if ($players->where('user_id', '!=', $players->first()->user_id)->count() > 0) {
        foreach ($players as $player) {

            if ($tmpCounter == 1) {
                if($player->user_type == 'player') {
                    $player->load('user');
                    $player->user->load('iconFlair');
                    $playerInstance[$player->user_id] = $player;

                    $tmpUser = [
                        'id' => $player->id,
                        'user_id' => $player->user_id,
                        'username' => $player->user->username,
                        'avatar' => ($player->user->avatar) ?: '/images/default-avatar.jpg',
                        'icon_flair' => $player->user->iconFlair->icon_url,
                        'points' => $player->points,
                        'user_type' => $player->user_type,
                        'remote_data' => json_decode($player->remote_data, true),
                    ];
                } else {
                    $player->load('tmpUser');
                    //    $playerInstance[$player->user_id] = $player;
                    $tmpUser = [
                        'id' => $player->id,
                        'user_id' => $player->user_id,
                        'tmp_user_id' => $player->tmpUser->id,
                        'username' => $player->tmpUser->username,
                        'avatar' => '/images/default-avatar.jpg',
                        'icon_flair' => '',
                        'points' => $player->points,
                        'user_type' => $player->user_type,
                        'remote_data' => json_decode($player->remote_data, true),
                    ];
                }

                $first = $tmpUser;// ($player->user_type == 'guest') ? $player->load('tmpUser') :  $player->load('user');
                $tmpCounter++;
                continue;
            }

            if ($tmpCounter == 2) {
                if($player->user_type == 'player') {
                    $player->load('user');
                    $player->user->load('iconFlair');
                    $playerInstance[$player->user_id] = $player;

                    $tmpUser = [
                        'id' => $player->id,
                        'user_id' => $player->user_id,
                        'username' => $player->user->username,
                        'avatar' => ($player->user->avatar) ?: '/images/default-avatar.jpg',
                        'icon_flair' => $player->user->iconFlair->icon_url,
                        'points' => $player->points,
                        'user_type' => $player->user_type,
                        'remote_data' => json_decode($player->remote_data, true),
                    ];
                } else {
                    $player->load('tmpUser');
                    //    $playerInstance[$player->user_id] = $player;
                    $tmpUser = [
                        'id' => $player->id,
                        'user_id' => $player->user_id,
                        'tmp_user_id' => $player->tmpUser->id,
                        'username' => $player->tmpUser->username,
                        'avatar' => '/images/default-avatar.jpg',
                        'icon_flair' => '',
                        'points' => $player->points,
                        'user_type' => $player->user_type,
                        'remote_data' => json_decode($player->remote_data, true),
                    ];
                }

                $second = $tmpUser;//($player->user_type == 'guest') ? $player->load('tmpUser') :  $player->load('user');
                $tmpCounter++;
                continue;
            }

            if ($tmpCounter == 3) {

                if($player->user_type == 'player') {
                    $player->load('user');
                    $player->user->load('iconFlair');
                    $playerInstance[$player->user_id] = $player;

                    $tmpUser = [
                        'id' => $player->id,
                        'user_id' => $player->user_id,
                        'username' => $player->user->username,
                        'avatar' => ($player->user->avatar) ?: '/images/default-avatar.jpg',
                        'icon_flair' => $player->user->iconFlair->icon_url,
                        'points' => $player->points,
                        'user_type' => $player->user_type,
                        'remote_data' => json_decode($player->remote_data, true),
                    ];
                } else {
                    $player->load('tmpUser');
                    //    $playerInstance[$player->user_id] = $player;
                    $tmpUser = [
                        'id' => $player->id,
                        'user_id' => $player->user_id,
                        'tmp_user_id' => $player->tmpUser->id,
                        'username' => $player->tmpUser->username,
                        'avatar' => '/images/default-avatar.jpg',
                        'icon_flair' => '',
                        'points' => $player->points,
                        'user_type' => $player->user_type,
                        'remote_data' => json_decode($player->remote_data, true),
                    ];
                }

                $third = $tmpUser;//($player->user_type == 'guest') ? $player->load('tmpUser') :  $player->load('user');
                $tmpCounter++;
                continue;
            }

            if($player->user_type == 'player') {
                $player->load('user');
                $player->user->load('iconFlair');
                $playerInstance[$player->user_id] = $player;

                $tmpUser = [
                    'id' => $player->id,
                    'user_id' => $player->user_id,
                    'username' => $player->user->username,
                    'avatar' => ($player->user->avatar) ?: '/images/default-avatar.jpg',
                    'icon_flair' => $player->user->iconFlair->icon_url,
                    'points' => $player->points,
                    'user_type' => $player->user_type,
                    'remote_data' => json_decode($player->remote_data, true),
                ];
            } else {
                $player->load('tmpUser');
                //    $playerInstance[$player->user_id] = $player;
                $tmpUser = [
                    'id' => $player->id,
                    'user_id' => $player->user_id,
                    'tmp_user_id' => $player->tmpUser->id,
                    'username' => $player->tmpUser->username,
                    'avatar' => '/images/default-avatar.jpg',
                    'icon_flair' => '',
                    'points' => $player->points,
                    'user_type' => $player->user_type,
                    'remote_data' => json_decode($player->remote_data, true),
                ];
            }

            $winners[] = $tmpUser;//($player->user_type == 'guest') ? $player->load('tmpUser') :  $player->load('user');
            $tmpCounter++;
        }
       // }

        //orders $winners by points
        usort($winners, function ($a, $b) {
            return $a->points <= $b->points;
        });

        $response = [
            //'winner' => ($players->first()->user_type == 'guest') ? $players->first()->load('tmpUser') : $players->first()->load('user'),
            'winner' => $first,
            'second' => $second,
            'third' => $third,
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
            if ($player->user_type == 'guest') {
                continue;
            }
            self::addUserStats($player->user_id, 'points', $player->points);
        }

        return ['status' => true, 'message' => 'User Stats updated'];
    }


    public static function closeGameInstance($gameId)
    {
        $gameInstance = GameInstances::where('id', $gameId)->where('user_id', Auth::user()->id)->first();
        if(!$gameInstance){
            return ['status' => false, 'message' => 'Could not find Game Instance'];
        }

        $gameInstance->status = 'closed';
        $gameInstance->save();

        return ['status' => true, 'message' => 'Game Instance closed'];
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


    public static function getUserActivePlayerInstances()
    {
        $playerInstances = PlayerInstances::where('user_id', Auth::user()->id)->where('status', '!=', 'completed')->get();
        return $playerInstances;
    }

    public static function getUserActiveGameInstances()
    {
        $gameInstances = GameInstances::where('user_id', Auth::user()->id)->where(function ($q) {
            return $q->where('status', '!=', 'completed') && $q->where('status', '!=', 'closed');
        })->get();
        if(!$gameInstances) {
            return [];
        }

        return $gameInstances;
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

    public static function getPlayersTotalGamesPlayed()
    {
        $totalGames = PlayerInstances::where('user_id', Auth::user()->id)->count();
        return $totalGames;
    }

    public static function addOrUpdateGameInstanceSetting($gameToken, $key, $value)
    {
        $gameInstance = GameInstances::where('token', $gameToken)->where('user_id', Auth::user()->id)->first();
        if(!$gameInstance){
            return ['status' => false, 'message' => 'Could not find Game Instance', 'gameInstanceSetting' => NULL];
        }

        $gameInstanceSetting = GameInstanceSettings::firstOrCreate([
            'game_instance_id' => $gameInstance->id,
            'key' => $key,
        ]);

        $gameInstanceSetting->value = $value;
        $gameInstanceSetting->save();

        return ['status' => true, 'message' => 'Game Instance Setting added', 'gameInstanceSetting' => $gameInstanceSetting];
    }

    public static function getGameInstanceSettings($gameToken, $key = null)
    {
        $gameInstance = GameInstances::where('token', $gameToken)->first();
        if(!$gameInstance){
            return ['status' => false, 'message' => 'Could not find Game Instance', 'gameInstanceSetting' => NULL];
        }

        if ($key == null) {
            $gameInstanceSetting = GameInstanceSettings::where('game_instance_id', $gameInstance->id)->get();
        } else {
            $gameInstanceSetting = GameInstanceSettings::where('game_instance_id', $gameInstance->id)->where('key', $key)->first();
            return ($gameInstanceSetting) ? $gameInstanceSetting->value : '';
        }

        if(!$gameInstanceSetting){
            return ['status' => false, 'message' => 'Could not find Game Instance Setting', 'gameInstanceSetting' => NULL];
        }
        return ['status' => true, 'message' => 'Game Instance Setting found', 'gameInstanceSetting' => $gameInstanceSetting];
    }

    public static function removeGameInstanceSetting($gameToken, $key)
    {
        $gameInstance = GameInstances::where('token', $gameToken)->where('user_id', Auth::user()->id)->first();
        if(!$gameInstance){
            return ['status' => false, 'message' => 'Could not find Game Instance', 'gameInstanceSetting' => NULL];
        }

        $gameInstanceSetting = GameInstanceSettings::where('game_instance_id', $gameInstance->id)->where('key', $key)->first();
        if(!$gameInstanceSetting){
            return ['status' => false, 'message' => 'Could not find Game Instance Setting', 'gameInstanceSetting' => NULL];
        }

        $gameInstanceSetting->delete();

        return ['status' => true, 'message' => 'Game Instance Setting removed', 'gameInstanceSetting' => $gameInstanceSetting];
    }

    public static function giveUsersGameCurrency($gameToken, $referenceId, $referenceType, $coefficient = 0.5)
    {
        //step 1 - collect all the players that participated the game
        $gameInstance = GameInstances::where('token', $gameToken)->where('user_id', Auth::user()->id)->first();
        if (!$gameInstance) {
            return false;
        }
        $playerInstances = PlayerInstances::where('game_instance_id', $gameInstance->id)->get();

        //step 2 - loop trough users
        foreach ($playerInstances as $player) {
            //step 3 - check if user have not already received currency for this game
            if($player->user_type == 'guest'){
                continue;
            }
            
            $gameCurrency = GameCurrency::where('user_id', $player->user_id)->where('game_instance_id', $gameInstance->id)->first();
            if ($gameCurrency) {
                continue;
            }

            $gameCurrency = GameCurrency::where('user_id', $player->user_id)->where('reference_id', $referenceId)->where('reference_type', $referenceType)->first();
            if ($gameCurrency) {
                continue;
            }

            //step 4.a - check game instance options if there is given custom coefficient
            $tmpCoefficient = self::getGameInstanceSettings($gameToken, 'coefficient');
            $coefficient = ($tmpCoefficient != '') ? $tmpCoefficient : $coefficient;

            //step 4 - calculate points users should receive for game (based on points and position and play time)
            $currency = round($player->points * $coefficient);
            //step 5 - add game currency to user
            GameCurrency::create([
                'type' => 'game_played',
                'amount' => $currency,
                'user_id' => $player->user_id,
                'game_instance_id' => $gameInstance->id,
                'reference_id' => $referenceId,
                'reference_type' => $referenceType,
                'note' => 'Game Instance: ' . $gameInstance->id . ' - ' . $gameInstance->title
            ]);
        }

        return true;
    }

    public static function getUsersGameCurrency()
    {
        return GameCurrency::where('user_id', Auth::user()->id)->sum('amount');
    }

    public static function createTmpUser($username)
    {
        $tmpUser = TmpUsers::create([
            'username' => $username,
            'token' => self::createToken(15),
            'tmp_user_id' => self::createToken(15),
        ]);

        return $tmpUser;
    }


    public static function getUsersIconFlair($userId = null)
    {
        if(is_null($userId) && !Auth::check()){
            return null;
        }

        if(is_null($userId) && Auth::check()){
            $userId = Auth::user()->id;
            $flairId = Auth::user()->icon_flair_id;
        } else {
            $user = User::where('id', $userId)->first();
            $flairId = $user->icon_flair_id;
        }

        return IconFlairs::where('id', $flairId)->first();
    }

    public static function getAvailableIconFlairs()
    {
        return IconFlairs::where('is_active', true)->get();
    }

    public static function changeIconFlair($flairId)
    {
        User::where('id', Auth::user()->id)->update([
            'icon_flair_id' => $flairId,
        ]);

        return true;
    }

    public static function getLeaderboard($token)
    {
        $gameInstance = GameInstances::where('token', $token)->first();
        $players = PlayerInstances::where('game_instance_id', $gameInstance['id'])->orderBy('points', 'desc')->get();

        foreach ($players as $player) {
            $user = ($player->user_type == 'guest') ? $player->tmpUser : $player->user;
            $player->username = $user['username'];
            //$player->user = ($player->user_type == 'guest') ? $player->load('tmpUser') : $player->load('user');
        }
            //dd($players);
        return $players;
    }
    
    public static function isFirstAnsweredCorrectlyToQuestion($gameInstanceId, $questionId, $answerId, $userId)
    {
        $firstCorrectAnswer = SubmittedAnswers::where('game_instance_id', $gameInstanceId)->where('question_id', $questionId)->where('answer_id', $answerId)->orderBy('created_at', 'DESC')->first();

        if ($userId == $firstCorrectAnswer->user_id) {
            return true;
        }
        
        return false;
    }

    public static function isFirstTextInputCorrectAnswer($gameInstanceId, $questionId, $correctAnswer, $userId)
    {
        $firstCorrectAnswer = SubmittedAnswers::where('game_instance_id', $gameInstanceId)->where('question_id', $questionId)->where('answer_custom_input', $correctAnswer)->orderBy('created_at', 'DESC')->first();
        if ($userId == $firstCorrectAnswer->user_id) {
            return true;
        }
        return false;
    }
}