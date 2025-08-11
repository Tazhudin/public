<?php

namespace Admin\Orchid\Screens\Feedback\Order;

use Admin\Models\Feedback\Faq;
use Admin\Orchid\Screens\Permission;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class FaqListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,object>
     */
    public function query(): iterable
    {
        return [
            'datarows' => Faq::filters()->defaultSort('created_at', 'DESC')->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Список вопросов-ответов';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Администрирование вопросов-ответов';
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
                ->title('Создание вопроса-ответа')
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
                Input::make('model.question')
                    ->title('Вопрос')
                    ->type('text')
                    ->max(255)
                    ->required(),
                TextArea::make('model.answer')
                    ->title('Ответ')
                    ->rows(7)
                    ->required(),
            ]))
                ->title('Редактирование вопроса-ответа')
                ->closeButton('Закрыть')
                ->applyButton('Сохранить')
                ->method('save')
                ->size(Modal::SIZE_LG)
                ->deferred('loadDataOnOpen'),

            // Таблица списка моделей
            Layout::table('datarows', [
                TD::make('id', 'ID'),
                TD::make('question', 'Вопрос'),
                TD::make('answer', 'Ответ'),
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
     * @return array<string,Faq>
     */
    public function loadDataOnOpen(Faq $model): iterable
    {
        return [
            'model' => $model
        ];
    }

    /**
     * Сохранить
     */
    public function save(Faq $model, Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'model.question' => ['required', 'string', 'max:255'],
            'model.answer' => ['required', 'string'],
        ]);

        $model->fill($request->collect('model')->toArray());
        $model->save();

        Toast::info('Запись сохранена');
        return redirect()->route('feedback.faq.list');
    }

    public function remove(int $id): \Illuminate\Http\RedirectResponse
    {
        Faq::findOrFail($id)->delete();

        Toast::info('Запись удалена');
        return redirect()->route('feedback.faq.list');
    }
}
