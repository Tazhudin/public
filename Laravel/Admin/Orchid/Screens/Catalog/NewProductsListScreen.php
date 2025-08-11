<?php

declare(strict_types=1);

namespace Admin\Orchid\Screens\Catalog;

use Admin\Orchid\Models\Catalog\NewProduct;
use Admin\Orchid\Models\Catalog\Product;
use Admin\Orchid\Screens\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class NewProductsListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,object>
     */
    public function query(): iterable
    {
        return [
            'datarows' => NewProduct::filters()->defaultSort('novelty_sort')->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Список новинок';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Просмотр новинок товаров';
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
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Добавить')
                ->modal('edit-model')
                ->title('Добавление записи')
                ->method('save')
                ->icon('bs.plus-circle')
                ->canSee(Auth::user()->hasAccess(Permission::CATALOG_EDIT)),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return string[]|Layout[]
     */
    public function layout(): iterable
    {
        return [
            // Модальное окно для модели
            Layout::modal('edit-model', Layout::rows([
                Relation::make('model.product_id')
                    ->title('Наименование')
                    ->fromModel(Product::class, 'name')
                    ->searchColumns('name')
                    ->chunk(50),
                DateTimer::make('model.novelty_to')
                    ->title('Срок новизны')
                    ->format('d.m.Y')
                    ->serverFormat()
                    ->required(),
                Input::make('model.novelty_sort')
                    ->title('Сортировка по новизне')
                    ->type('number')
                    ->required(),
            ]))
                ->title('Редактирование')
                ->closeButton('Закрыть')
                ->applyButton('Сохранить')
                ->method('save')
                ->size(Modal::SIZE_LG)
                ->deferred('loadDataOnOpen'),

            // Таблица списка моделей
            Layout::table('datarows', [
                TD::make('product_id', 'Наименование')
                    ->cantHide()
                    ->filter(Input::make())
                    ->render(function ($model) {
                        if ($model->product) {
                            return Link::make($model->product->name)
                                ->route('catalog.product.show', $model->product->id);
                        }
                        return '<span class="text-danger">Товар удалён</span>';
                    }),
                TD::make('novelty_to', 'Срок новизны')
                    ->cantHide()
                    ->sort()
                    ->render(fn($model) => $model->novelty_to?->format('d.m.Y H:i:s') ?? '-'),
                TD::make('novelty_sort', 'Сортировка по новизне')
                    ->cantHide()
                    ->sort(),
                TD::make('Действия')
                    ->cantHide()
                    ->width('100px')
                    ->render(fn($model) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            ModalToggle::make('Редактировать')
                                ->icon('bs.pencil')
                                ->modal('edit-model', [
                                    'model' => $model->product_id
                                ])
                                ->method('save')
                                ->canSee(Auth::user()->hasAccess(Permission::CATALOG_EDIT)),
                            Button::make('Удалить')
                                ->icon('bs.trash')
                                ->confirm('Действительно хотите удалить запись?')
                                ->method('remove', [
                                    'id' => $model->product_id,
                                ])
                                ->canSee(Auth::user()->hasAccess(Permission::CATALOG_EDIT))
                        ])),
            ]),
        ];
    }

    /**
     * Получить данные модели
     *
     * @return array<string,NewProduct>
     */
    public function loadDataOnOpen(NewProduct $model): iterable
    {
        return [
            'model' => $model
        ];
    }

    /**
     * Сохранить
     */
    public function save(NewProduct $model, Request $request): RedirectResponse
    {
        $request->validate([
            'model.product_id' => ['required', 'uuid'],
            'model.novelty_to' => ['required', 'date'],
            'model.novelty_sort' => ['required', 'integer'],
        ]);

        $model->fill($request->collect('model')->toArray());
        $model->save();

        Toast::info('Запись сохранена');
        return redirect()->route('catalog.newproduct.list');
    }

    public function remove(string $id): RedirectResponse
    {
        NewProduct::findOrFail($id)->delete();

        Toast::info('Запись удалена');
        return redirect()->route('catalog.newproduct.list');
    }
}
