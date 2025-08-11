<?php

namespace Admin\Orchid\Components;

use Illuminate\View\Component;

class Json extends Component
{
    public function __construct(
        protected mixed $value
    ) {
    }

    public function render(): string
    {
        $json = json_encode($this->value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return "<pre class='bg-light p-3 rounded border'><code>$json</code></pre>";
    }
}
