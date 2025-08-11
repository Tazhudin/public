<?php

namespace Admin\Orchid\Components;

use Illuminate\View\Component;

class SuccessOrFailure extends Component
{
    public function __construct(
        protected mixed $value,
        protected bool $isSuccess
    ) {
    }

    public function render(): string
    {
        $class = $this->isSuccess ? 'text-bg-success' : 'text-bg-danger';

        return "<span class='text-white badge $class'>$this->value</span>";
    }
}
