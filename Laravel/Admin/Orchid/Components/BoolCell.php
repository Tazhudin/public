<?php

namespace Admin\Orchid\Components;

use Illuminate\View\Component;

class BoolCell extends Component
{
    public function __construct(
        public bool $value
    ) {
    }

    public function render(): string
    {
        $text = $this->value ? 'Да' : 'Нет';
        $class = $this->value ? 'success' : 'danger';

        return "<span class='badge text-bg-$class'>$text</span>";
    }
}
