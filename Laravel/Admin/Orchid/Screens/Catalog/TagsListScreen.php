<?php

namespace Admin\Orchid\Screens\Catalog;

use Admin\Orchid\Models\Catalog\Tag;
use Admin\Orchid\Screens\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class TagsListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,object>
     */
    public function query(): iterable
    {
        return [
            'datarows' => Tag::filters()->defaultSort('created_at')->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Список тэгов';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Администрирование списка тэгов товаров';
    }

    /**
     * The permissions required to access this screen.
     *
     * @return array<string>
     */
    public function permission(): ?iterable
    {
        return [
            Permission::CATALOG_READ,
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
            Layout::modal('edit-model', Layout::rows([
                Input::make('model.color')
                    ->title('Цвет')
                    ->type('text')
                    ->max(7)
                    ->required(),
            ]))
                ->title('Редактирование тэга товара')
                ->closeButton('Закрыть')
                ->applyButton('Сохранить')
                ->method('save')
                ->size(Modal::SIZE_LG)
                ->deferred('loadDataOnOpen'),

            // Таблица списка моделей
            Layout::table('datarows', [
                TD::make('id', 'ID'),
                TD::make('name', 'Название'),
                TD::make('color', 'Цвет'),
                TD::make('Действия')
                    ->cantHide()
                    ->width('100px')
                    ->render(fn($model) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            ModalToggle::make('Редактировать цвет')
                                ->modal('edit-model', [
                                    'model' => $model->id
                                ])
                                ->method('save')
                                ->icon('bs.pencil')
                                ->canSee(Auth::user()->hasAccess(Permission::CATALOG_EDIT)),
                        ])),
            ]),
        ];
    }

    /**
     * Получить данные модели
     *
     * @return array<string,OrderEvaluationVariant>
     */
    public function loadDataOnOpen(Tag $model): iterable
    {
        return [
            'model' => $model
        ];
    }

    /**
     * Сохранить
     */
    public function save(Tag $model, Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'model.color' => ['required', 'ascii', 'max:7'],
        ]);

        $model->fill($request->collect('model')->toArray());
        $model->save();

        Toast::info('Запись сохранена');
        return redirect()->route('catalog.tag.list');
    }

    public function remove(int $id): \Illuminate\Http\RedirectResponse
    {
        Tag::findOrFail($id)->delete();

        Toast::info('Запись удалена');
        return redirect()->route('catalog.tag.list');
    }
}
