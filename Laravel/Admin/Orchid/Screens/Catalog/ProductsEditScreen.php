<?php

declare(strict_types=1);

namespace Admin\Orchid\Screens\Catalog;

use Admin\Models\PriceAndStock\ProductPrice;
use Admin\Models\PriceAndStock\ProductStock;
use Admin\Orchid\Layouts\Catalog\ProductPricesLayout;
use Admin\Orchid\Layouts\Catalog\ProductPropsLayout;
use Admin\Orchid\Models\Catalog\CategoryShow;
use Admin\Orchid\Models\Catalog\Product;
use Admin\Orchid\Screens\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Components\Cells\DateTime;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ProductsEditScreen extends Screen
{
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint
    public $model;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,object>
     */
    public function query(Product $product): iterable
    {
        $product->load(['images', 'properties', 'properties.property', 'priceAndStock', 'reserve.stock']);
        return [
            'model' => $product,
            'images' => $product->images,
            'props' => $product->properties,
            'prices' => $product->priceAndStock,
            'reserve' => $product->reserve,
            'show-in-categories' => $product->showInCategories->pluck('id')->toArray(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Редактирование товара';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Редактирование товаров';
    }

    /**
     * The permissions required to access this screen.
     *
     * @return array<string>
     */
    public function permission(): ?iterable
    {
        return [
            Permission::CATALOG_EDIT,
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
            Button::make(__('Save'))
                ->icon('bs.check-circle')
                ->method('save'),

            Button::make(__('Remove'))
                ->icon('bs.trash3')
                ->confirm('Действительно хотите удалить запись?')
                ->method('remove')
                ->canSee($this->model->exists),
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
            // Модальное окно для остатков
            Layout::modal(
                'edit-stock',
                Layout::rows([
                    Input::make('model.value')
                        ->title('Остаток')
                        ->type('number')
                        ->min(0)
                        ->required(),
                ])
            )
                ->title('Редактирование остатков')
                ->closeButton('Закрыть')
                ->applyButton('Сохранить')
                ->deferred('loadStockOnOpen'),

            // Модальное окно для цен
            Layout::modal(
                'edit-price',
                Layout::rows([
                    Input::make('model.value')
                        ->title('Цена')
                        ->type('number')
                        ->min(0)
                        ->required(),
                ])
            )
                ->title('Редактирование цен')
                ->closeButton('Закрыть')
                ->applyButton('Сохранить')
                ->deferred('loadPriceOnOpen'),

            // Таблица списка моделей
            Layout::split([
                Layout::rows([
                    Input::make('model.name')
                        ->type('text')
                        ->max(255)
                        ->required()
                        ->title('Наименование')
                        ->placeholder('Наименование')
                        ->help('Наименование для сайта'),
                    Input::make('model.description')
                        ->type('text')
                        ->max(255)
                        ->required()
                        ->title('Описание')
                        ->placeholder('Описание'),
                    Relation::make('model.category_id')
                        ->title('Основная категория')
                        ->fromModel(CategoryShow::class, 'name')
                        ->applyScope('active')
                        ->searchColumns('name')
                        ->chunk(50),
                    Relation::make('show-in-categories')
                        ->title('Показывать в категориях')
                        ->fromModel(CategoryShow::class, 'name')
                        ->searchColumns('name')
                        ->chunk(50)
                        ->disabled()
                        ->multiple(),
                    Input::make('model.popularity')
                        ->type('number')
                        ->required()
                        ->title('Популярность'),
                    Group::make([
                        CheckBox::make('model.is_new')
                            ->sendTrueOrFalse()
                            ->title('Новинка'),
                        CheckBox::make('model.is_active')
                            ->sendTrueOrFalse()
                            ->title('Активноcть')
                    ])
                ]),
                Layout::blank([
                    Layout::legend('model', [
                        Sight::make('code', 'Код товара'),
                        Sight::make('id', 'Id товара'),
                        Sight::make('category_id', 'Id категории'),
                        Sight::make('imported_at', 'Дата появления')
                            ->render(fn($model) => $model->imported_at?->format('d.m.Y')),
                        Sight::make('created_at', 'Время создания')
                            ->render(fn($model) => $model->created_at?->format('d.m.Y H:i:s')),
                        Sight::make('updated_at', 'Время изменения')
                            ->render(fn($model) => $model->updated_at?->format('d.m.Y H:i:s')),
                    ]),
                ]),
            ]),
            ProductPricesLayout::class,
            Layout::table('reserve', [
                TD::make('stock.name', 'Склад'),
                TD::make('reserve_key', 'Ключ'),
                TD::make('quantity', 'Количество')->alignRight(),
                TD::make('created_at', 'Дата')->asComponent(DateTime::class)->alignRight(),
            ])->title('Резервы'),
            Layout::split([
                Layout::blank([
                    \Admin\Orchid\Layouts\Catalog\ProductImagesLayout::class,
                ]),
                ProductPropsLayout::class,
            ]),
        ];
    }

    public function save(Product $product, Request $request): RedirectResponse
    {
        $request->validate([
            'model.name' => ['required', 'string', 'max:255'],
            'model.category_id' => ['required', 'uuid'],
            'show-in-categories' => ['array'],
            'model.popularity' => ['required', 'integer'],
            'model.order_position_limit' => ['integer', 'nullable'],
            'model.is_new' => ['boolean'],
            'model.is_active' => ['boolean'],
        ]);

        $product->fill($request->get('model'));
        $product->fill(['show_in_categories' => $request->get('show-in-categories')]);
        $product->save();
        Toast::info('Запись сохранена');
        return redirect()->route('catalog.product.list');
    }

    public function remove(Product $product): RedirectResponse
    {
        $product->delete();

        Toast::info('Запись удалена');
        return redirect()->route('catalog.product.list');
    }

    /**
     * Получить данные остатков
     *
     * @return array<string, ProductStock>
     */
    public function loadStockOnOpen(ProductStock $model): iterable
    {
        return [
            'model' => $model
        ];
    }

    public function saveStock(ProductStock $model, Request $request): RedirectResponse
    {
        $request->validate([
            'model.value' => ['required', 'integer'],
        ]);
        $model->fill($request->collect('model')->toArray());
        $model->save();

        Toast::info('Запись сохранена');
        return redirect()->route('catalog.product.edit', ['product' => $model->product_id]);
    }

    /**
     * Получить данные цены
     *
     * @return array<string, ProductPrice>
     */
    public function loadPriceOnOpen(ProductPrice $model): iterable
    {
        return [
            'model' => $model
        ];
    }

    public function savePrice(ProductPrice $model, Request $request): RedirectResponse
    {
        $request->validate([
            'model.value' => ['required', 'integer'],
        ]);
        $model->fill($request->collect('model')->toArray());
        $model->save();

        Toast::info('Запись сохранена');
        return redirect()->route('catalog.product.edit', ['product' => $model->product_id]);
    }
}
