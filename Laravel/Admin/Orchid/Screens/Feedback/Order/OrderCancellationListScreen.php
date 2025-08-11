<?php

namespace Admin\Orchid\Screens\Feedback\Order;

use Admin\Models\Feedback\OrderCancellation;
use Admin\Orchid\Components\ArrayStrings;
use Admin\Orchid\Components\OrderStatus;
use Admin\Orchid\Screens\Permission;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class OrderCancellationListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,object>
     */
    public function query(): iterable
    {
        return [
            'datarows' => OrderCancellation::with(['customer', 'order'])->filters()
                ->defaultSort('created_at', 'DESC')
                ->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Список отмен заказов';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Просмотр обратной связи отмен заказов';
    }

    /**
     * The permissions required to access this screen.
     *
     * @return array<string>
     */
    public function permission(): ?iterable
    {
        return [
            Permission::FEEDBACK_READ,
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            // Модальное окно для модели
            Layout::modal('show-model', Layout::legend('model', [
                Sight::make('reason', 'Причины отмены заказа')->asComponent(ArrayStrings::class),
                Sight::make('comment', 'Комментарий к отмене'),
                Sight::make('order_status', 'Статус до отмены')->render(function (OrderCancellation $model) {
                    return (new OrderStatus($model->order_status, $model->order->isPaid()))->render();
                }),
                Sight::make('order_id', 'Заказ')
                    ->render(fn($model) => $model->order ? Link::make($model->order->number)
                    ->route('order.detail', $model->order->number) : null),
                Sight::make('customer_id', 'Покупатель')
                    ->render(fn($model) => $model->customer ? Link::make(sprintf(
                        '%s %s (%s)',
                        $model->customer->name,
                        $model->customer->second_name,
                        $model->customer->phone_number,
                    ))
                    ->route('customer.detail', $model->customer->id) : null),
            ]))
                ->title('Просмотр отмены заказа')
                ->closeButton('Закрыть')
                ->withoutApplyButton()
                ->size(Modal::SIZE_LG)
                ->deferred('loadDataOnOpen'),

            // Таблица списка моделей
            Layout::table('datarows', [
                TD::make('created_at', 'Дата создания')->asComponent(DateTimeSplit::class),
                TD::make('reason', 'Причины отмены заказа')->asComponent(ArrayStrings::class),
                TD::make('comment', 'Комментарий к отмене'),
                TD::make('order_id', 'Заказ')
                    ->render(fn($model) => $model->order ? Link::make($model->order->number)
                        ->route('order.detail', $model->order->number) : null),
                TD::make('order_status', 'Статус до отмены')->render(function (OrderCancellation $model) {
                    return (new OrderStatus($model->order_status, $model->order->isPaid()))->render();
                }),

                TD::make('Действия')
                    ->cantHide()
                    ->width('100px')
                    ->render(fn($model) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            ModalToggle::make('Посмотреть')
                                ->modal('show-model', [
                                    'model' => $model->id
                                ])
                                ->icon('bs.eye'),
                        ])),
            ]),
        ];
    }

    /**
     * Получить данные модели
     *
     * @return array<string,\Admin\Models\Feedback\OrderCancellation>
     */
    public function loadDataOnOpen(OrderCancellation $model): iterable
    {
        return [
            'model' => $model
        ];
    }
}
