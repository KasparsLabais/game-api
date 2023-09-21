<?php

namespace PartyGames\GameApi\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'name', 'token', 'creators_name', 'creators_url', 'prefix',
    ];
}