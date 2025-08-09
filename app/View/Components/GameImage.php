<?php

namespace App\View\Components;

use Illuminate\View\Component;

class GameImage extends Component
{
    public $game;
    public $class;
    public $alt;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($game, $class = '', $alt = null)
    {
        $this->game = $game;
        $this->class = $class;
        $this->alt = $alt ?? $game->name;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.game-image');
    }
}