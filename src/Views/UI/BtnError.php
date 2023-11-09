<?php

namespace PartyGames\GameApi\Views\UI;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BtnError extends Component
{

    public $text;
    public $icon;
    public $link;
    public $target;
    public $type;
    public $isALink;

    public $onClick;

    public function __construct($text = '', $icon = '', $link = '', $target = '', $type = 'button', $isALink = true, $onClick = null)
    {
        $this->text = $text;
        $this->icon = $icon;
        $this->link = $link;
        $this->target = $target;
        $this->type = $type;
        $this->isALink = $isALink;
        $this->onClick = $onClick;
    }

    public function render()
    {
        return view('game-api::components.ui.btn-error');
    }

}