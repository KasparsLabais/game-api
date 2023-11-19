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
        Blade::component('game-api::components.notification-success', 'notification-success');
        Blade::component('game-api::components.notification-error', 'notification-error');
        Blade::component('game-api::components.notification-info', 'notification-info');

        Blade::component('game-api::components.ui.btn-primary', 'btn-primary');
        Blade::component('game-api::components.ui.btn-error', 'btn-error');
        Blade::component('game-api::components.ui.btn-disabled', 'btn-disabled');
        Blade::component('game-api::components.ui.btn-alternative', 'btn-alternative');
        Blade::component('game-api::components.ui.btn-premium', 'btn-premium');

        Blade::component('game-api::components.ui.card', 'card');
        Blade::component('game-api::components.ui.section', 'section');
        Blade::component('game-api::components.ui.modal', 'modal');
    }

    public function register()
    {
        $this->app->bind('game-api', function() {
            return new GameApi;
        });
    }

}