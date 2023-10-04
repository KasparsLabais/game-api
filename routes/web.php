<?php

use Illuminate\Support\Facades\Route;

Route::get('/trivia/test', 'BaseController@index');
Route::get('/api/points', 'BaseController@getPlayersPoints');