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

        public function iconFlair() {
            return $this->hasOne(IconFlairs::class, 'id', 'icon_flair_id');
        }

        public function border() {
            return $this->hasOne(Borders::class, 'id', 'border_id');
        }
}