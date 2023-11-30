<?php

namespace PartyGames\GameApi\Models;

use Illuminate\Database\Eloquent\Model;

class TmpUsers extends Model
{
    protected $table = 'tmp_users';
    protected $fillable = ['username', 'token', 'tmp_user_id'];

}