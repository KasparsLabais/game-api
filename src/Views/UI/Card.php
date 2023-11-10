<?php

namespace PartyGames\GameApi\Views\UI;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Card extends Component
{
    public $title = '';

    public function __construct($title = '')
    {
        $this->title = $title;
    }

    public function render()
    {
        return view('game-api::components.ui.card');
    }
}