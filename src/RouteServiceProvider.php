<?php

namespace PartyGames\GameApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'PartyGames\GameApi\Http\Controllers';

    public function map()
    {
        Route::namespace($this->namespace)->middleware('web')->group(__DIR__ . '/../routes/web.php');
    }

}