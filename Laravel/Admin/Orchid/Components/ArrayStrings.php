<?php

namespace Admin\Orchid\Components;

use Illuminate\View\Component;

class ArrayStrings extends Component
{
    public function __construct(
        protected mixed $value
    ) {
    }

    public function render(): string
    {
        if (!is_array($this->value)) {
            return '';
        }

        $html = '<ul>';

        foreach ($this->value as $value) {
            $html .= "<li>$value</li>";
        }

        $html .= '</ul>';

        return $html;
    }
}
