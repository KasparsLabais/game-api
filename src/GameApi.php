<?php

namespace PartyGames\GameApi;

class GameApi
{

    protected $name;

    public function __construct($name = "John")
    {
        $this->name = $name;
    }

    public function hello()
    {
        return "Woobly Jumbli {$this->name}";
    }

}