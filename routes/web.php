<?php

use Illuminate\Support\Facades\Route;

Route::get('/trivia/test', 'BaseController@index');
Route::get('/api/points', 'BaseController@getPlayersPoints');
Route::get('/api/games', 'BaseController@getAllGames');

Route::post('/api/game-instance-setting', 'BaseController@addGameInstanceSetting');
Route::post('/api/game-instance', 'BaseController@editGameInstance');