<?php

namespace PartyGames\GameApi\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerInstances extends Model {

    protected $fillable = ['user_id', 'game_instance_id', 'points', 'status', 'remote_data'];
    protected $table = 'player_instances';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gameInstance()
    {
        return $this->belongsTo(GameInstances::class);
    }
}