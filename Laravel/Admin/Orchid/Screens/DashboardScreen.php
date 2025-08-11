<?php

declare(strict_types=1);

namespace Admin\Orchid\Screens;

use Admin\Orchid\Metrics\AvailableProductsCountQuery;
use Admin\Orchid\Metrics\OrdersCountQuery;
use Illuminate\Support\Facades\Cache;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

//phpcs:disable SlevomatCodingStandard
class DashboardScreen extends Screen
{
    public function __construct(
        private readonly AvailableProductsCountQuery $availableProductsCountQuery,
        private readonly OrdersCountQuery $ordersCountQuery
    ) {
    }

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $availableProductsCount = Cache::remember('metrics.available_products_quantity', 5, function () {
            return $this->availableProductsCountQuery->count();
        });

        $todayOrdersCount = Cache::remember('metrics.today_orders_quantity', 3, function () {
            return $this->ordersCountQuery->countTodayOrders();
        });

        $todayTotalOrderSum = Cache::remember('metrics.today_total_orders_sum', 3, function () {
            return $this->ordersCountQuery->todayTotalSum();
        });

        $todayAverageOrdersSum = Cache::remember('metrics.today_average_orders_sum', 3, function () {
            return $this->ordersCountQuery->todayAverageOrdersSum();
        });

        return [
            'metrics' => [
                'available_products_quantity' => ['value' => $availableProductsCount],
                'today_orders_quantity' => ['value' => $todayOrdersCount],
                'today_total_orders_sum' => ['value' => $todayTotalOrderSum . ' ₽'],
                'today_average_orders_sum' => ['value' => $todayAverageOrdersSum . ' ₽'],
            ]
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Хорошего рабочего настроения!';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Приветствуем в административной панели системы оформления заказов Близко!';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        $todayOrdersQuantityByStock = Cache::remember('metrics.today_orders_quantity_by_stock_table', 3, function () {
            return collect($this->ordersCountQuery->countTodayOrdersByStock())
                ->map(fn($row) => ['key' => $row->stock_name, 'values' => [
                    $row->quantity,
                    $row->total_sum.' ₽',
                    $row->average_sum.' ₽',
                ]])
                ->all();
        });

        return [
            Layout::metrics([
                'Количество товаров' => 'metrics.available_products_quantity',
                'Заказов за сегодня' => 'metrics.today_orders_quantity',
                'Средний чек за сегодня' => 'metrics.today_average_orders_sum',
                'Выручка за сегодня' => 'metrics.today_total_orders_sum'
            ]),
            Layout::view('metrics.table', [
                'title' => 'Статистика по складам',
                'header' => [
                    'key' => 'Склад',
                    'values' => [
                        'Количество',
                        'Выручка',
                        'Средний чек',
                    ]
                ],
                'body' => $todayOrdersQuantityByStock
            ])
        ];
    }

    private function todayOrdersQuantityByStock(): Table
    {
        return (new class () extends Table {
            protected $target = 'metrics.today_orders_quantity_by_stock';

            protected function columns(): array
            {
                return [
                    TD::make('stock', 'Склад')->cantHide(),
                    TD::make('quantity', 'Количество')->alignRight()->cantHide()
                ];
            }

            protected function compact(): bool
            {
                return true;
            }
        })->title('Заказов на сегодня по складам');
    }
}
