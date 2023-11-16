<?php

namespace PartyGames\GameApi\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerInstances extends Model {

    protected $fillable = ['user_id', 'game_instance_id', 'points', 'status', 'remote_data', 'user_type'];
    protected $table = 'player_instances';

    public function user()
    {
        if($this->user_type == 'tmp_user' || $this->user_type == 'guest') {
            return $this->belongsTo(TmpUsers::class, 'user_id', 'tmp_user_id');
        }
        return $this->belongsTo(User::class);
    }

    public function tmpUser() {
        return $this->belongsTo(TmpUsers::class, 'user_id', 'tmp_user_id');
    }

    public function gameInstance()
    {
        return $this->belongsTo(GameInstances::class);
    }
}