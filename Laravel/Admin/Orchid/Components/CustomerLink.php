<?php

namespace Admin\Orchid\Components;

use Admin\Orchid\Models\Customer\Customer;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\View\Component;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Color;

class CustomerLink extends Component
{
    public function __construct(
        private readonly ?Customer $customer,
        private readonly bool $short = false
    ) {
    }

    public function render(): Htmlable | string
    {
        if (is_null($this->customer)) {
            return '-';
        }

        return Link::make($this->customer->getDisplayDescription($this->short))
            ->type(Color::BASIC)
            ->route('customer.detail', $this->customer->id);
    }
}
