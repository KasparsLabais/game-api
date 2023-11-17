<?php

namespace PartyGames\GameApi\Models;

use Illuminate\Database\Eloquent\Model;
class GameInstances extends Model
{

    protected $fillable = ['title', 'game_id', 'user_id', 'status', 'token', 'remote_data', 'pin'];
    protected $table = 'game_instances';


    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function playerInstances()
    {
        return $this->hasMany(PlayerInstances::class, 'game_instance_id', 'id');
    }

    public function gameInstanceSettings()
    {
        return $this->hasMany(GameInstanceSettings::class, 'game_instance_id', 'id');
    }

    public function getGameInstanceSetting($key = '') {
        $setting = $this->gameInstanceSettings()->where('key', $key)->first();
        if ($setting) {
            return $setting->value;
        }
        return null;
    }
}
