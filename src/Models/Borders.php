<?php

namespace PartyGames\GameApi\Models;

use Illuminate\Database\Eloquent\Model;

class Borders extends Model
{
    protected $table = 'borders';
    protected $primaryKey = 'id';

    protected $fillable = ['name', 'color', 'is_active', 'is_premium'];
}
