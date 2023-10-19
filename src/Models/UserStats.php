<?php

use Illuminate\Database\Eloquent\Model;
class UserStats extends Model
{
    protected $table = 'user_stats';
    protected $fillable = [
        'user_id',
        'key',
        'value'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}