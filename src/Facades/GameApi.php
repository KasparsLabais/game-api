<?php

namespace PartyGames\GameApi\Facades;

use Illuminate\Support\Facades\Facade;

class GameApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'game-api';
    }
}