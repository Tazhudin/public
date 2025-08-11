<?php

namespace Admin\Orchid\Screens\Feedback\Order;

use Admin\Models\Feedback\OrderEvaluation;
use Admin\Orchid\Components\ArrayImages;
use Admin\Orchid\Components\ArrayStrings;
use Admin\Orchid\Screens\Permission;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class OrderEvaluationListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,object>
     */
    public function query(): iterable
    {
        return [
            'datarows' => OrderEvaluation::with(['customer', 'order'])->filters()
                ->defaultSort('created_at', 'DESC')
                ->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Список оценок заказов';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Просмотр оценок заказов';
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
                Sight::make('evaluation', 'Оценка'),
                Sight::make('comment', 'Комментарий к оценке'),
                Sight::make('comments', 'Предустановленный комментарий')
                    ->asComponent(ArrayStrings::class),
                Sight::make('order_id', 'Заказ')
                    ->render(fn($model) => $model->order ? Link::make($model->order->number)
                    ->route('order.detail', $model->order->number) : null),
                Sight::make('user_id', 'Покупатель')
                    ->render(fn($model) => $model->customer ? Link::make(sprintf(
                        '%s %s (%s)',
                        $model->customer->name,
                        $model->customer->second_name,
                        $model->customer->phone_number,
                    ))
                    ->route('customer.detail', $model->customer->id) : null),
                Sight::make('images', 'Изображения')
                    ->asComponent(ArrayImages::class),

            ]))
                ->title('Просмотр оценки заказа')
                ->closeButton('Закрыть')
                ->withoutApplyButton()
                ->size(Modal::SIZE_LG)
                ->deferred('loadDataOnOpen'),

            // Таблица списка моделей
            Layout::table('datarows', [
                TD::make('id', 'ID'),
                TD::make('evaluation', 'Оценка'),
                TD::make('comment', 'Комментарий к оценке'),
                TD::make('comments', 'Предустановленный комментарий')
                    ->asComponent(ArrayStrings::class),
                TD::make('order_id', 'Заказ')
                    ->render(fn($model) => $model->order ? Link::make($model->order->number)
                    ->route('order.detail', $model->order->number) : null),
                TD::make('user_id', 'Покупатель')
                    ->render(fn($model) => $model->customer ? Link::make(sprintf(
                        '%s %s (%s)',
                        $model->customer->name,
                        $model->customer->second_name,
                        $model->customer->phone_number,
                    ))->route('customer.detail', $model->customer->id) : null),
                TD::make('created_at', 'Дата создания')
                    ->sort()
                    ->render(fn($model) => $model->created_at->format('d.m.Y H:i:s')),

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
     * @return array<string,\Admin\Models\Feedback\OrderEvaluation>
     */
    public function loadDataOnOpen(OrderEvaluation $model): iterable
    {
        return [
            'model' => $model
        ];
    }
}
