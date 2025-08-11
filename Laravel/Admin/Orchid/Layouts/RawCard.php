<?php

namespace Admin\Orchid\Layouts;

use Orchid\Screen\Layout;
use Orchid\Screen\Repository;

class RawCard extends Layout
{
    public function __construct(
        private readonly string $target
    ) {
    }

    public function build(Repository $repository): mixed
    {
        $this->query = $repository;

        if (!$this->isSee()) {
            return null;
        }

        return view('card', [
            'content' => $repository->get($this->target)
        ]);
    }
}
