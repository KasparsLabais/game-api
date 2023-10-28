<?php

namespace PartyGames\GameApi\Views;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NotificationSuccess extends Component
{
    public $message;

    public function __construct($message = '')
    {
        $this->message = $message;
    }

    public function render()
    {
        return view('game-api::components.notification-success');
    }
}