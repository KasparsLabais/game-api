<?php

namespace PartyGames\GameApi\Models;

use Illuminate\Database\Eloquent\Model;

class GameCurrency extends Model
{
    protected $table = 'game_currency';
    protected $fillable = [
        'type',
        'amount',
        'user_id',
        'game_instance_id',
        'reference_id',
        'reference_type',
        'note'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}