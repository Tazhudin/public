<?php

namespace Admin\Orchid\Components;

use Illuminate\View\Component;

class OrderStatus extends Component
{
    public function __construct(
        protected string $status,
        protected bool $isPaid = false
    ) {
    }

    public function render(): string
    {
        $statusName = match ($this->status) {
            '' => 'Не определен',
            'NEW' => $this->isPaid ? 'Принят' : 'Заказ не оплачен',
            'COLLECTING' => 'Комплектуется',
            'DELIVERING' => 'Выехал к вам',
            'COMPLETED' => 'Доставлен',
            'CANCELLED' => 'Отменен'
        };

        $statusBadgeType = match ($this->status) {
            'NEW' => $this->isPaid ? 'bg-primary' : 'text-bg-warning',
            'COLLECTING', 'DELIVERING', '' => 'bg-info',
            'COMPLETED' => 'text-bg-success',
            'CANCELLED' => 'text-bg-danger'
        };

        return "<span class='badge $statusBadgeType'>$statusName</span>";
    }
}
