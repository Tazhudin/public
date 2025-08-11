<?php

namespace Admin\Orchid\Screens\Feedback\Order;

use Admin\Models\Feedback\OrderEvaluationVariant;
use Admin\Orchid\Components\ArrayStrings;
use Admin\Orchid\Screens\Permission;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class OrderEvaluationVariantListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,object>
     */
    public function query(): iterable
    {
        return [
            'datarows' => OrderEvaluationVariant::filters()->defaultSort('created_at', 'DESC')->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Список вариантов оценок';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Администрирование вариантов оценок заказа';
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
                ->title('Создание варианта оценки')
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
                Input::make('model.comment')
                    ->title('Комментарий к оценке')
                    ->type('text')
                    ->max(255)
                    ->required(),
                Select::make('model.evaluations')
                    ->title('Оценки')
                    ->options([ 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5' ])
                    ->multiple()
                    ->required(),
            ]))
                ->title('Редактирование варианта оценки')
                ->closeButton('Закрыть')
                ->applyButton('Сохранить')
                ->method('save')
                ->size(Modal::SIZE_LG)
                ->deferred('loadDataOnOpen'),

            // Таблица списка моделей
            Layout::table('datarows', [
                TD::make('id', 'ID'),
                TD::make('comment', 'Комментарий к оценке'),
                TD::make('evaluations', 'Оценки')
                    ->asComponent(ArrayStrings::class),
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
    public function loadDataOnOpen(OrderEvaluationVariant $model): iterable
    {
        return [
            'model' => $model
        ];
    }

    /**
     * Сохранить
     */
    public function save(OrderEvaluationVariant $model, Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'model.comment' => ['required', 'string', 'max:255'],
            'model.evaluations' => ['required'],
        ]);

        $model->fill($request->collect('model')->toArray());
        $model->save();

        Toast::info('Запись сохранена');
        return redirect()->route('feedback.orderevaluationvariant.list');
    }

    public function remove(int $id): \Illuminate\Http\RedirectResponse
    {
        OrderEvaluationVariant::findOrFail($id)->delete();

        Toast::info('Запись удалена');
        return redirect()->route('feedback.orderevaluationvariant.list');
    }
}
