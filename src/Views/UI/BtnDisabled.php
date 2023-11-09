<?php

namespace PartyGames\GameApi\Views\UI;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BtnDisabled extends Component
{

    public $text;
    public $icon;
    public $link;
    public $target;
    public $type;

    public function __construct($text = '', $icon = '', $link = '', $target = '', $type = 'button')
    {
        $this->text = $text;
        $this->icon = $icon;
        $this->link = $link;
        $this->target = $target;
        $this->type = $type;
    }

    public function render()
    {
        return view('game-api::components.ui.btn-disabled');
    }

}