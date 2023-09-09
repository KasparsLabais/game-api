<?php

namespace PartyGames\GameApi\Models;

use Illuminate\Database\Eloquent\Model;
class GameInstances extends Model
{

    protected $fillable = ['title', 'game_id', 'user_id', 'status'];
    protected $table = 'game_instances';

}
