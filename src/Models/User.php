<?php

namespace PartyGames\GameApi\Models;

use Illuminate\Database\Eloquent\Model;
class User extends Model
{
        protected $table = 'users';

        public function gameInstances()
        {
            return $this->hasMany(GameInstances::class);
        }

        public function games()
        {
            return $this->hasMany(Game::class);
        }
}