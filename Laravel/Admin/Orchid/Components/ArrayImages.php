<?php

namespace Admin\Orchid\Components;

use Illuminate\View\Component;

class ArrayImages extends Component
{
    public function __construct(
        protected mixed $value
    ) {
    }

    public function render(): string
    {
        return is_array($this->value) ? implode(' ', array_map(
            fn($value) => "<img src=\"{$value['url']}\" style=\"max-height:300px\" />",
            $this->value
        )) : '';
    }
}
