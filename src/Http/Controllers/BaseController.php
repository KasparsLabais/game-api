<?php

namespace PartyGames\GameApi\Http\Controllers;

class BaseController
{
    public function index()
    {
        return view('game-api::index');
    }

}