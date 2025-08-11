<?php

namespace Admin\Orchid\Screens\Order;

use Admin\Enums\ClientApp;
use Admin\Orchid\Components\Badge;
use Admin\Orchid\Components\CustomerLink;
use Admin\Orchid\Components\Json;
use Admin\Orchid\Components\Phone;
use Admin\Orchid\Models\Catalog\Product;
use Admin\Orchid\Models\Customer\Customer;
use Admin\Orchid\Screens\Order\Component\OrderStatus;
use Admin\Orchid\Screens\Permission;
use Library\ValueObject\DeliveryTime;
use Orchid\Screen\Components\Cells\Currency;
use Orchid\Screen\Components\Cells\DateTime;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Repository;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Order\Api\Query\ControlPanel\Order\Detail\GetOrderDetailQuery;
use Order\Api\Query\ControlPanel\Order\Detail\OrderHistory;
use Order\Api\Query\ControlPanel\Order\Detail\OrderItem;
use PriceAndStock\Infrastructure\Repository\Db\Model\Stock;
use Symfony\Component\Uid\Uuid;

class OrderDetailScreen extends Screen
{
    public ?Repository $order = null;

    public function __construct(
        private readonly GetOrderDetailQuery $getOrderDetailQuery
    ) {
    }

    //phpcs:ignore
    public function query(string $orderNumber): iterable
    {
        $order = $this->getOrderDetailQuery->getOrder($orderNumber);
        $customer = Customer::find($order->main->customerId);

        if (Uuid::isValid($order->stockId)) {
            $stockName = Stock::find($order->stockId)?->name ?? '-';
        } else {
            $stockName = '-';
        }

        $productIds = $order->items->pluck('productId')->toArray();
        $products = Product::whereIn('id', $productIds)->with(['images'])->get()->keyBy('id');

        return [
            'order' => new Repository([
                'createdAt' => $order->main->createdAt,
                'number' => $order->main->number,
                'customer' => $customer,
                'status' => [
                    'status' => $order->main->status,
                    'isPaid' => $order->main->isPaid
                ],
                'address' => $order->address,
                'amount' => $order->main->amount,
                'deliveryPrice' => $order->deliveryPrice,
                'stockId' => $stockName,
                'promocode' => $order->main->promocode ?? '-',
                'clientApp' => $order->main->clientApp,
            ]),
            'order_items' => $order->items->map(function (OrderItem $item) use ($products) {
                return new Repository([
                    'productId' => $item->productId,
                    'productImage' => $products->get($item->productId)?->images->first()?->url ?? null,
                    'productName' => $item->productName,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'oldPrice' => $item->oldPrice,
                    'metadata' => $item->metadata
                ]);
            }),
            'order_meta' => new Repository($order->metta->getAssocArray()),
            'order_history' => $order->history->map(function (OrderHistory $item) {
                return new Repository([
                    'date' => $item->date,
                    'event' => $item->event,
                    'persona' => $item->persona,
                    'payload' => $item->payload
                ]);
            }),
            'additional_info' => new Repository([
                'deliveryTime' => $order->main->deliveryTime,
                'commentForWarehousemen' => $order->additionalInfo->commentForWarehousemen,
                'commentForCourier' => $order->additionalInfo->commentForCourier,
                'courierPhone' => $order->additionalInfo->courierPhone,
                'leaveAtTheDoor' => $order->additionalInfo->leaveAtTheDoor ? 'Да' : 'Нет',
                'isTest' => $order->additionalInfo->isTest ? 'Да' : 'Нет',
                'skipPayment' => $order->additionalInfo->skipPayment ? 'Да' : 'Нет'
            ])
        ];
    }

    public function name(): ?string
    {
        return 'Заказ №' . $this->order->get('number') .
            ' (' . $this->order->get('customer')->getDisplayDescription() . ')';
    }

    /**
     * @return string[]|null
     */
    public function permission(): ?iterable
    {
        return [Permission::SHOW_ORDER];
    }

    /**
     * @return Layout[]
     * @throws \ReflectionException
     */
    public function layout(): iterable
    {
        return [
            Layout::tabs([
                'Заказ' => [
                    Layout::columns([
                        Layout::legend('order', [
                            Sight::make('createdAt', 'Дата')->asComponent(DateTime::class),
                            Sight::make('status', 'Статус')->asComponent(OrderStatus::class),
                            Sight::make('customer', 'Покупатель')->asComponent(CustomerLink::class),
                            Sight::make('address', 'Адрес доставки'),
                            Sight::make('amount', 'Сумма')->asComponent(Currency::class, [
                                'thousand_separator' => ' ',
                                'decimals' => 0,
                                'after' => '₽'
                            ]),
                            Sight::make('deliveryPrice', 'Доставка')->asComponent(Currency::class, [
                                'thousand_separator' => ' ',
                                'decimals' => 0,
                                'after' => '₽'
                            ]),
                            Sight::make('stockId', 'Склад'),
                            Sight::make('promocode', 'Промокод')->asComponent(Badge::class),
                            Sight::make('clientApp', 'Источник')->render(fn(Repository $order)
                                => $order->get('clientApp') ? ClientApp::{$order->get('clientApp')}->value : '-'),
                        ]),
                        Layout::legend('additional_info', [
                            Sight::make('deliveryTime', 'Доставка')->render(fn(Repository $order)
                                => $this->renderDeliveryTime($order->get('deliveryTime'))),
                            Sight::make('commentForWarehousemen', 'Комментарий сборщику'),
                            Sight::make('commentForCourier', 'Комментарий курьеру'),
                            Sight::make('courierPhone', 'Телефон курьера')->asComponent(Phone::class),
                            Sight::make('leaveAtTheDoor', 'Оставить у двери')->asComponent(Badge::class),
                            Sight::make('isTest', 'Не отправлять в 1С')->asComponent(Badge::class),
                            Sight::make('skipPayment', 'Пропустить оплату')->asComponent(Badge::class)
                        ])
                    ]),
                    Layout::table('order_items', [
                        TD::make(title: 'Товар')->width(80)->render(function (Repository $model) {
                            return "<img src='{$model->get('productImage')}' class='img-card'
                              style='max-height: 80px; max-width: 80px; object-fit: contain;'>";
                        }),
                        TD::make('productName', '')
                            ->width(500),
                        TD::make('quantity', 'Количество')->alignCenter(),
                        TD::make('price', 'Цена')->asComponent(Currency::class, [
                            'thousand_separator' => ' ',
                            'decimals' => 0,
                            'after' => '₽'
                        ]),
                        TD::make('metadata', 'Метаданные')
                            ->style('max-width: 100px')
                            ->asComponent(Json::class)
                    ])->title('Товары')
                ],
                'История изменений' => Layout::table('order_history', [
                    TD::make('date', 'Дата')->asComponent(DateTimeSplit::class),
                    TD::make('event', 'Событие'),
                    TD::make('persona', 'Актор'),
                    TD::make('payload', 'Данные')
                        ->style('max-width: 400px')
                        ->asComponent(Json::class)
                ]),
                'Метаданные' =>
                    Layout::legend('order_meta', [
                        Sight::make('STOCK_ID', 'id склада')
                            ->render(fn(Repository $repo) => $repo->get('STOCK_ID') ?: 'Не указано'),
                        Sight::make('CLIENT_APP', 'Приложение клиента')
                            ->render(fn(Repository $repo) => $repo->get('CLIENT_APP') ?: 'Не указано'),
                        Sight::make('SKIP_PAYMENT', 'Пропустить оплату')
                            ->render(fn(Repository $repo) => $repo->get('SKIP_PAYMENT') ? 'Да' : 'Нет'),
                        Sight::make('LEAVE_AT_THE_DOOR', 'Оставить у двери')
                            ->render(fn(Repository $repo) => $repo->get('LEAVE_AT_THE_DOOR') ? 'Да' : 'Нет'),
                        Sight::make('COMMENT_FOR_COURIER', 'Комментарий для курьера')
                            ->render(fn(Repository $repo) => $repo->get('COMMENT_FOR_COURIER') ?: 'Не указано'),
                        Sight::make('PROMISED_DELIVERY_TIME', 'Запланированное время доставки')
                            ->render(fn(Repository $repo) => $repo->get('PROMISED_DELIVERY_TIME')),
                        Sight::make('COMMENT_FOR_WAREHOUSEMEN', 'Комментарий для складчиков')
                            ->render(fn(Repository $repo) => $repo->get('COMMENT_FOR_WAREHOUSEMEN') ?: 'Не указано'),
                    ]),
                ])
        ];
    }

    private function renderDeliveryTime(?DeliveryTime $deliveryTime): string
    {
        if ($deliveryTime === null) {
            return 'Экспресс';
        }

        $startTime = $deliveryTime->startDateTime->format('H:i');
        $endTime = $deliveryTime->endDateTime->format('H:i');

        $date = $deliveryTime->startDateTime->format('d.m.Y');
        $time = "$startTime-$endTime";

        return "Отложенная: <b>$date</b> / <b>$time</b>";
    }
}
