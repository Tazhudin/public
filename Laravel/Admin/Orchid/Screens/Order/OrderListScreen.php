<?php

namespace Admin\Orchid\Screens\Order;

use Admin\Enums\ClientApp;
use Admin\Orchid\Components\Badge;
use Admin\Orchid\Components\CustomerLink;
use Admin\Orchid\Models\Customer\Customer;
use Admin\Orchid\Screens\Order\Component\OrderStatus;
use Admin\Orchid\Screens\Permission;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Library\ValueObject\DeliveryTime;
use Library\ValueObject\GetListOptions;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\Currency;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Repository;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Order\Api\Query\ControlPanel\GetOrderListQuery;
use Order\Api\Query\ControlPanel\Order\OrderMainInfo;

class OrderListScreen extends Screen
{
    public function __construct(
        private readonly GetOrderListQuery $query
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function query(): iterable
    {
        $orderNumberFilter = request()->input('filter.number');
        $orderSourceFilter = array_filter(request()->input('filter.clientApp') ?? []);
        $customerPhoneFilter = request()->input('filter.customer');
        $filterCustomerIds = optional($customerPhoneFilter, function (string $phone) {
            return Customer::where('phone_number', 'like', "%$phone%")->pluck('id')->all();
        }) ?? [];

        $getListOptions = GetListOptions::createFromRequest(request: request(), defaultSize: 13);
        $items = $this->query->getOrders(
            options: $getListOptions,
            searchByNumber: $orderNumberFilter,
            searchByClientApp: $orderSourceFilter,
            searchByCustomerIds: $filterCustomerIds
        );
        $itemsCustomerIds = $items->map(fn(OrderMainInfo $order) => $order->customerId)->all();
        $customers = Customer::whereIn('id', $itemsCustomerIds)->get()->keyBy('id');

        $orders = $items->map(fn(OrderMainInfo $order) => new Repository([
            'createdAt' => $order->createdAt,
            'number' => $order->number,
            'customer' => $customers->get($order->customerId),
            'deliveryTime' => $order->deliveryTime,
            'status' => [
                'status' => $order->status,
                'isPaid' => $order->isPaid
            ],
            'amount' => $order->amount,
            'promocode' => $order->promocode ?? '-',
            'clientApp' => $order->clientApp,
        ]));

        return [
            'orders' => new LengthAwarePaginator(
                items: $orders,
                total: $items->totalCount,
                perPage: $getListOptions->limit,
                currentPage: $getListOptions->getPage(),
                options: [
                    'path' => Paginator::resolveCurrentPath(),
                ]
            )
        ];
    }

    public function name(): ?string
    {
        return 'Заказы';
    }

    /**
     * @return Permission[]|null
     */
    public function permission(): ?iterable
    {
        return [Permission::SHOW_ORDER];
    }

    /**
     * @return Layout[]
     */
    public function layout(): iterable
    {
        return [
            new class () extends Table {
                //phpcs:ignore
                protected $target = 'orders';

                //phpcs:ignore
                protected function columns(): array
                {
                    return [
                        TD::make('createdAt', 'Дата')->asComponent(DateTimeSplit::class),
                        TD::make('number', '№ заказа')->render(function (Repository $order) {
                            return Link::make($order->get('number'))
                                ->type(Color::BASIC)
                                ->route('order.detail', $order->get('number'));
                        })->filter(),
                        TD::make('customer', 'Покупатель')->asComponent(CustomerLink::class, [
                            'short' => true
                        ])->filter(),
                        TD::make('status', 'Статус')->asComponent(OrderStatus::class),
                        TD::make('amount', 'Сумма')->asComponent(Currency::class, [
                            'thousand_separator' => ' ',
                            'decimals' => 0,
                            'after' => '₽'
                        ])->alignRight(),
                        TD::make('deliveryTime', 'Доставка')
                            ->alignRight()
                            ->render(fn(Repository $order) => $this->renderDeliveryTime($order->get('deliveryTime'))),
                        TD::make('promocode', 'Промокод')
                            ->alignRight()
                            ->asComponent(Badge::class),
                        TD::make('clientApp', 'Источник')
                            ->filter(Select::make()->multiple()
                                ->options(['' => 'Все'] + ClientApp::asArray()))
                            ->render(fn(Repository $order) => $order->get('clientApp')
                                ? ClientApp::{$order->get('clientApp')}->value : '-'),
                    ];
                }

                protected function hoverable(): bool
                {
                    return true;
                }

                protected function compact(): bool
                {
                    return true;
                }

                private function renderDeliveryTime(?DeliveryTime $deliveryTime): string
                {
                    if ($deliveryTime === null) {
                        return '<span class="badge text-black">Экспресс</span>';
                    }

                    $startTime = $deliveryTime->startDateTime->format('H:i');
                    $endTime = $deliveryTime->endDateTime->format('H:i');

                    $date = $deliveryTime->startDateTime->format('d.m.Y');
                    $time = "$startTime-$endTime";

                    return "<div class='badge text-bg-dark' style='text-align: right!important;'>$date<br/>$time</div>";
                }
            }
        ];
    }
}
