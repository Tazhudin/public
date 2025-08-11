<?php

namespace Admin\Orchid\Screens\Order\Component;

use Illuminate\View\Component;

class OrderStatus extends Component
{
    /**
     * @param array<string, mixed> $status
     */
    public function __construct(
        private readonly array $status,
    ) {
    }

    public function render(): string
    {
        [$status, $isPaid] = [$this->status['status'], $this->status['isPaid']];

        $statusName = match ($status) {
            'NEW' => $isPaid ? 'Принят' : 'Заказ не оплачен',
            'COLLECTING' => 'Комплектуется',
            'DELIVERING' => 'Выехал к вам',
            'COMPLETED' => 'Доставлен',
            'CANCELLED' => 'Отменен'
        };

        $statusBadgeType = match ($status) {
            'NEW' => $isPaid ? 'text-bg-primary' : 'text-bg-warning',
            'COLLECTING', 'DELIVERING' => 'text-bg-info',
            'COMPLETED' => 'text-bg-success',
            'CANCELLED' => 'text-bg-danger'
        };

        return "<span class='badge $statusBadgeType'>$statusName</span>";
    }
}
