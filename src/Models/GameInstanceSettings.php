<?php

namespace PartyGames\GameApi\Models;

use Illuminate\Database\Eloquent\Model;
use PartyGames\GameApi\Models\GameInstances;

class GameInstanceSettings extends Model
{
    protected $table = 'game_instance_settings';
    protected $fillable = [
        'game_instance_id',
        'key',
        'value'
    ];

    public function gameInstance()
    {
        return $this->belongsTo(GameInstances::class, 'game_instance_id', 'id');
    }
}