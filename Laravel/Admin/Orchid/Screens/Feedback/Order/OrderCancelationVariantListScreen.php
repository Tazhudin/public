<?php

namespace Admin\Orchid\Screens\Feedback\Order;

use Admin\Models\Feedback\OrderCancelationVariant;
use Admin\Orchid\Screens\Permission;
use App\Orchid\Screens\Feedback\Order\OrderEvaluationVariant;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class OrderCancelationVariantListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,object>
     */
    public function query(): iterable
    {
        return [
            'datarows' => OrderCancelationVariant::filters()->defaultSort('created_at', 'DESC')->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Список вариантов отмены заказа';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Администрирование вариантов отмены заказа';
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
            ModalToggle::make('Создать')
                ->modal('edit-model')
                ->title('Создание варианта отмены заказа')
                ->method('save')
                ->icon('bs.plus-circle'),
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
            Layout::modal('edit-model', Layout::rows([
                Input::make('model.reason')
                    ->title('Вариант отмены заказа')
                    ->type('text')
                    ->max(255)
                    ->required(),
            ]))
                ->title('Редактирование варианта отмены заказа')
                ->closeButton('Закрыть')
                ->applyButton('Сохранить')
                ->method('save')
                ->size(Modal::SIZE_LG)
                ->deferred('loadDataOnOpen'),

            // Таблица списка моделей
            Layout::table('datarows', [
                TD::make('id', 'ID'),
                TD::make('reason', 'Вариант отмены заказа'),
                TD::make('Действия')
                    ->cantHide()
                    ->width('100px')
                    ->render(fn($model) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            ModalToggle::make('Редактировать')
                                ->modal('edit-model', [
                                    'model' => $model->id
                                ])
                                ->method('save')
                                ->icon('bs.pencil'),
                            Button::make('Удалить')
                                ->icon('bs.trash')
                                ->confirm('Действительно хотите удалить запись?')
                                ->method('remove', [
                                    'id' => $model->id,
                                ])
                        ])),
            ]),
        ];
    }

    /**
     * Получить данные модели
     *
     * @return array<string,OrderEvaluationVariant>
     */
    public function loadDataOnOpen(OrderCancelationVariant $model): iterable
    {
        return [
            'model' => $model
        ];
    }

    /**
     * Сохранить
     */
    public function save(OrderCancelationVariant $model, Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'model.reason' => ['required', 'string', 'max:255'],
        ]);

        $model->fill($request->collect('model')->toArray());
        $model->save();

        Toast::info('Запись сохранена');
        return redirect()->route('feedback.ordercancelationvariant.list');
    }

    public function remove(int $id): \Illuminate\Http\RedirectResponse
    {
        OrderCancelationVariant::findOrFail($id)->delete();

        Toast::info('Запись удалена');
        return redirect()->route('feedback.ordercancelationvariant.list');
    }
}
