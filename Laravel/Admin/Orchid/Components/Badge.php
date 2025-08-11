<?php

namespace Admin\Orchid\Components;

use Illuminate\View\Component;

class Badge extends Component
{
    public function __construct(
        protected mixed $value,
        protected string $type = 'light',
    ) {
    }

    public function render(): string
    {
        return "<span class='badge text-bg-$this->type'>$this->value</span>";
    }
}
