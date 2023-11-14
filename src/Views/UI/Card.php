<?php

namespace PartyGames\GameApi\Views\UI;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Card extends Component
{
    public $title = '';
    public $addHeader = true;

    public function __construct($title = '', $addHeader = true)
    {
        $this->title = $title;
        $this->addHeader = $addHeader;
    }

    public function render()
    {
        return view('game-api::components.ui.card');
    }
}