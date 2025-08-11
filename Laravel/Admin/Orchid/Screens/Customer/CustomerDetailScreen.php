<?php

namespace Admin\Orchid\Screens\Customer;

use Admin\Orchid\Components\Badge;
use Admin\Orchid\Components\SuccessOrFailure;
use Admin\Orchid\Models\Customer\Customer;
use Admin\Orchid\Models\Customer\CustomerAddress;
use Admin\Orchid\Models\Customer\CustomerNotification;
use Admin\Orchid\Screens\Order\Component\OrderStatus;
use Admin\Orchid\Screens\Permission;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Library\ValueObject\GetListOptions;
use Library\ValueObject\Phone;
use Notification\Model\NotificationStatus;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Menu;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Components\Cells\Currency;
use Orchid\Screen\Components\Cells\DateTime;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Layouts\TabMenu;
use Orchid\Screen\Repository;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;
use Orchid\Support\Color;
use Order\Api\Query\ControlPanel\GetOrderListQuery;
use Order\Api\Query\ControlPanel\Order\OrderMainInfo;
use Orchid\Support\Facades\Layout;

class CustomerDetailScreen extends Screen
{
    public ?Customer $customer = null;
    public ?string $tab1 = null;

    public function __construct(
        private readonly GetOrderListQuery $query
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function query(string $customerId, ?string $tab1 = null): iterable
    {
        $getListOptions = GetListOptions::createFromRequest(request: request(), defaultSize: 7);
        $customerOrdersList = $this->query->getOrders(
            options: $getListOptions,
            searchByCustomerIds: [$customerId]
        );

        $ordersItems = $customerOrdersList->map(fn(OrderMainInfo $order) => new Repository([
            'createdAt' => $order->createdAt,
            'number' => $order->number,
            'status' => [
                'status' => $order->status,
                'isPaid' => $order->isPaid
            ],
            'amount' => $order->amount,
            'promocode' => $order->promocode ?? '-'
        ]));

        return [
            'tab1' => $tab1,
            'customer' => Customer::find($customerId),
            'orders' => new LengthAwarePaginator(
                items: $ordersItems,
                total: $customerOrdersList->totalCount,
                perPage: $getListOptions->limit,
                currentPage: $getListOptions->getPage(),
                options: [
                    'path' => Paginator::resolveCurrentPath(),
                ]
            ),
            'notifications' => CustomerNotification::filters()
                ->where('customer_id', $customerId)
                ->defaultSort('created_at', 'desc')
                ->paginate(6),
            'addresses' => CustomerAddress::filters()
                ->where('customer_id', $customerId)
                ->defaultSort('created_at', 'desc')
                ->paginate(10),
        ];
    }

    public function name(): ?string
    {
        return $this->customer->getDisplayDescription();
    }

    /**
     * @return string[]|null
     */
    public function permission(): ?iterable
    {
        return [Permission::CUSTOMER];
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return Layout[]
     * @throws \ReflectionException
     */
    public function layout(): iterable
    {
        return [
            Layout::modal('address-show', [
                Layout::legend('address', [
                    Sight::make('id', 'Id'),
                    Sight::make('name', 'Название'),
                    Sight::make('comment', 'Комментарий'),
                    Sight::make('customer_id', 'Id-пользователя'),
                    Sight::make('created_at', 'Дата создания')->asComponent(DateTimeSplit::class),
                    Sight::make('updated_at', 'Дата обновления')->asComponent(DateTimeSplit::class),
                    Sight::make('address', 'Адрес')->render(fn($model) => $model->address->description()),
                    Sight::make('address', 'Координаты')->render(fn($model) => 'широта: ' . $model->address->coordinate->latitude . ' долгота: ' . $model->address->coordinate->longitude),
                    Sight::make('address', 'Частный дом')->render(fn($model) => $model->address->isPrivateHouse() ? 'Да' : 'Нет'),
                ])
            ])->async('asyncGetAddressData'),
            Layout::legend('customer', [
                Sight::make('phone_number', 'Номер телефона')->render(fn(Customer $customer) => Phone::fromString($customer->phone_number)->formattedString()),
                Sight::make('name', 'Имя'),
                Sight::make('second_name', 'Фамилия'),
            ]),
            $this->tabMenu1(),
            $this->ordersTable()->canSee($this->tab1 == 'orders' || $this->tab1 == null),
            $this->notificationsTable()->canSee($this->tab1 == 'notifications'),
            $this->addresses()->canSee($this->tab1 == 'addresses'),
        ];
    }

    public function asyncGetAddressData(CustomerAddress $address): array
    {
        return ['address' => $address];
    }

    private function tabMenu1(): TabMenu
    {
        return new class () extends TabMenu {
            /**
             * @return Menu[]
             */
            protected function navigations(): iterable
            {
                $ordersLink = Menu::make('Заказы')
                    ->route('customer.detail', [
                        'customerId' => $this->query->get('customer')->id,
                        'tab1' => 'orders'
                    ]);

                if ($this->query->get('tab1') == null) {
                    $ordersLink = $ordersLink->active([URL::current()]);
                }

                return [
                    $ordersLink,
                    Menu::make('Уведомления')
                        ->route('customer.detail', [
                            'customerId' => $this->query->get('customer')->id,
                            'tab1' => 'notifications'
                        ]),
                    Menu::make('Адреса')
                        ->route('customer.detail', [
                            'customerId' => $this->query->get('customer')->id,
                            'tab1' => 'addresses'
                        ])
                ];
            }
        };
    }

    private function ordersTable(): Table
    {
        return new class () extends Table {
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
                    }),
                    TD::make('status', 'Статус')->asComponent(OrderStatus::class),
                    TD::make('amount', 'Сумма')->asComponent(Currency::class, [
                        'thousand_separator' => ' ',
                        'decimals' => 0,
                        'after' => '₽'
                    ])->alignRight(),
                    TD::make('promocode', 'Промокод')
                        ->alignRight()
                        ->asComponent(Badge::class),
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
        };
    }

    private function notificationsTable(): Table
    {
        return \Orchid\Support\Facades\Layout::table('notifications', [
            TD::make('created_at', 'Дата')->sort()->asComponent(DateTime::class),
            TD::make('message', 'Сообщение'),
            TD::make('type')->render(function (CustomerNotification $notification) {
                return (new Badge($notification->type->name))->render();
            }),
            TD::make('status')->render(function (CustomerNotification $notification) {
                return (new SuccessOrFailure(
                    $notification->status->name,
                    $notification->status == NotificationStatus::SENT
                ))->render();
            }),
            TD::make('action', 'Действия')->render(function (CustomerNotification $notification) {
                return Link::make('Показать')
                    ->type(Color::INFO)
                    ->route('customer_notification.detail', $notification->id);
            }),
        ]);
    }

    private function addresses(): Table
    {
        return \Orchid\Support\Facades\Layout::table('addresses', [
            TD::make('address', 'Адрес')
                ->render(function ($model) {
                    return $model->address->description();
                }),
            TD::make('isPrivateHouse', 'Частный дом')
                ->render(function ($model) {
                    return  $model->address->isPrivateHouse() ? 'Да' : 'Нет';
                }),
            TD::make('Действия')
                ->cantHide()
                ->width('100px')
                ->render(fn($model) => DropDown::make()->icon('bs.three-dots-vertical')->list([
                    ModalToggle::make('Показать')
                        ->modal('address-show', ['address' => $model->id])->icon('bs.pencil')
                ]))
        ]);
    }

}
