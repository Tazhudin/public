<?php

namespace Admin\Orchid\Components;

use Illuminate\View\Component;

class Phone extends Component
{
    public function __construct(
        public string $phone
    ) {
    }

    public function render(): string
    {
        return \Library\ValueObject\Phone::fromString($this->phone)?->formattedString() ?? '';
    }
}
