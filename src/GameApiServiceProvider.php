<?php

namespace PartyGames\GameApi;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Blade;
class GameApiServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //$this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views/', 'game-api');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../resources/static' => public_path('vendor/game-api')],
            'game-api-assets'
        );

        Blade::component('game-api::components.points', 'points');
    }

    public function register()
    {
        $this->app->bind('game-api', function() {
            return new GameApi;
        });
    }

}