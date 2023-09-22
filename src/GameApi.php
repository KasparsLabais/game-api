<?php

namespace PartyGames\GameApi;

use Illuminate\Support\Str;
use PartyGames\GameApi\Models\Game;
use PartyGames\GameApi\Models\GameInstances;

class GameApi
{

    protected $name;
    protected $dummy = 0;

    public function __construct()
    {
    }

    public static function createGameInstance($token, $title, $userId)
    {
        $game = Game::where('token', '=', $token)->first();

        if (!$game) {
            return ['status' => false, 'gameInstance' => NULL, 'message' => 'Could not find Game'];
        }

        $newGameInstance = GameInstances::create([
            'title' => $title,
            'game_id' => $game['id'],
            'user_id' => $userId,
            'status' => 'created',
            'token' => GameAPi::createToken(15),
        ]);

        if (!$newGameInstance) {
            return ['status' => false, 'gameInstance' => NULL, 'message' => 'Failed to create game instance.'];
        }

        return ['status' => true, 'gameInstance' => $newGameInstance, 'message' => 'Woop Woop Game Instance is created!'];
    }

    public static function getGameInstance()
    {

    }

    public static function closeGameInstance()
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