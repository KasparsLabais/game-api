<?php

namespace PartyGames\GameApi\Http\Controllers;

use Illuminate\Http\Request;
use PartyGames\GameApi\GameApi;

class BaseController
{
    public function index()
    {
        return view('game-api::index');
    }

    public function getPlayersPoints(Request $request)
    {
        $gameInstanceId = $request->get('gameInstanceId');
        $playerId = $request->get('playerId');

        $response = GameApi::getPlayerInstance($gameInstanceId, $playerId);
        return response()->json([
            'success' => true,
            'message' => 'Player instance found',
            'points' => $response['playerInstance']['points']
        ]);
    }


    public function getAllGames(Request $request)
    {
        $response = GameApi::getAllGames();
        return response()->json([
            'success' => true,
            'message' => 'Games found',
            'games' => $response['games']
        ]);
    }

}