<?php

namespace Admin\Orchid\Components;

use Illuminate\View\Component;

class Html extends Component
{
    public function __construct(
        public string $content
    ) {
    }

    public function render(): string
    {
        return <<<'blade'
        {!!$content!!}
        blade;
    }
}
