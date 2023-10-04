<?php

namespace PartyGames\GameApi\Views;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Points extends Component
{
    public $points;

    public function __construct($points)
    {
        $this->points = $points;
    }

    public function render()
    {
        return view('game-api::components.points');
    }
}