<?php

namespace PartyGames\GameApi\Models;

use Illuminate\Database\Eloquent\Model;

class IconFlairs extends Model
{
    protected $table = 'icon_flairs';
    protected $primaryKey = 'id';

    protected $fillable = ['name', 'icon_url', 'is_active', 'is_premium'];
}