<?php

namespace Admin\Orchid\Components;

use Illuminate\View\Component;

class Collection extends Component
{
    public function __construct(
        protected mixed $value
    ) {
    }

    public function render(): string
    {
        return $this->value ? implode(' ', $this->value->pluck('name')->map(
            fn($name) => "<span class=\"badge bg-primary\">{$name}</span>"
        )->toArray()) : '';
    }
}
