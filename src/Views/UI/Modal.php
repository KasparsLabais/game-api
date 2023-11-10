<?php

namespace PartyGames\GameApi\Views\UI;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    public $title = '';
    public $id = '';
    public $size = '';
    public $target = '';

    public function __construct($title = '', $id = '', $target = '', $size = '')
    {
        $this->title = $title;
        $this->target = $target;
        $this->id = $id;
        $this->size = $size;
    }

    public function render()
    {
        return view('game-api::components.ui.modal');
    }
}