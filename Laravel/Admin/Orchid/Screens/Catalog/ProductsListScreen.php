<?php

declare(strict_types=1);

namespace Admin\Orchid\Screens\Catalog;

use Admin\Orchid\Models\Catalog\Category;
use Admin\Orchid\Models\Catalog\Product;
use Admin\Orchid\Models\Catalog\ProductShowInCategories;
use Admin\Orchid\Screens\Permission;
use Illuminate\Http\Request;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Menu;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Layouts\TabMenu;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ProductsListScreen extends Screen
{
    public ?string $tab = null;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,object>
     */
    public function query(Category $category, Request $request): iterable
    {
        $this->tab = $request->input('tab', 'products');

        $productsQuery = Product::query()->filters();
        if ($category->id) {
            $productsQuery->where('category_id', $category->id);
        }

        $duplicatedProductIds = ProductShowInCategories::query()->pluck('product_id');

        return [
            'products' => $this->tab === 'products' ?
                $productsQuery->paginate(10, ['*'], 'datarows_page') : null,
            'duplicated_products' => $this->tab === 'duplicated_products' ?
                $productsQuery->whereIn('id', $duplicatedProductIds)
                    ->paginate(10, ['*'], 'duplicated_page') : null
        ];
    }


    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Список товаров';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Просмотр товаров';
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
            Button::make(__('Сбросить популярность'))
                ->icon('bs.trash3')
                ->confirm('Обновить популярность у всех товаров?')
                ->method('resetProductsPopular'),
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
            $this->tabMenu(),
            \Admin\Orchid\Layouts\Catalog\CatalogProductFiltersLayout::class,
            $this->productsTable()->canSee($this->tab == 'products' || $this->tab == null),
            $this->duplicatedProductsTable()->canSee($this->tab == 'duplicated_products'),
        ];
    }

    private function tabMenu(): TabMenu
    {
        return new class () extends TabMenu {
            /**
             * @return iterable<Menu>
             */
            protected function navigations(): iterable
            {
                return [
                    Menu::make('Все товары')
                        ->route('catalog.product.list', [
                            'tab' => 'products'
                        ]),
                    Menu::make('Дублирующие товары')
                        ->route('catalog.product.list', [
                            'tab' => 'duplicated_products'
                        ])
                ];
            }
        };
    }

    private function productsTable(): Table
    {
        return new class () extends Table {
            // phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
            protected $target = 'products';

            /**
             * @return array<TD>
             */
            protected function columns(): array
            {
                return [
                    TD::make('code', 'Код товара')
                        ->sort()
                        ->cantHide()
                        ->filter(Input::make())
                        ->render(fn($model) => Link::make((string)$model->code)
                            ->route('catalog.product.edit', $model)),
                    TD::make('name', 'Наименование')
                        ->sort()
                        ->cantHide()
                        ->filter(Input::make())
                        ->width('550px')
                        ->render(function ($model) {
                            return Link::make($model->name)
                                ->route('catalog.product.edit', $model)
                                ->class('line-break');
                        }),
                    TD::make('category_id', 'Категория')
                        ->filter(Input::make())
                        ->render(fn($model) => $model->category ? Link::make($model->category->name)
                            ->route('catalog.product.bycategory', $model->category) : '-'),
                    TD::make('popularity', 'Популярность')
                        ->sort()
                        ->cantHide()
                        ->filter(Input::make()),
                    TD::make('Действия')
                        ->cantHide()
                        ->width('100px')
                        ->render(fn($model) => DropDown::make()
                            ->icon('bs.three-dots-vertical')
                            ->list([
                                Link::make('Редактировать')
                                    ->icon('bs.pencil')
                                    ->route('catalog.product.edit', $model->id),
                                Button::make('Удалить')
                                    ->icon('bs.trash')
                                    ->confirm('Действительно хотите удалить запись?')
                                    ->method('remove', ['id' => $model->id]),
                            ])),
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

    private function duplicatedProductsTable(): Table
    {
        return new class () extends Table {
            //phpcs:ignore
            protected $target = 'duplicated_products';

            //phpcs:ignore
            protected function columns(): array
            {
                return [
                    TD::make('code', 'Код товара')
                        ->sort()
                        ->cantHide()
                        ->filter(Input::make())
                        ->render(fn($model) => Link::make((string)$model->code)
                            ->route('catalog.product.edit', $model)),
                    TD::make('name', 'Наименование')
                        ->sort()
                        ->cantHide()
                        ->filter(Input::make())
                        ->width('550px')
                        ->render(function ($model) {
                            return Link::make($model->name)
                                ->route('catalog.product.edit', $model)
                                ->class('line-break');
                        }),
                    TD::make('category_id', 'Категория')
                        ->filter(Input::make())
                        ->render(fn($model) => $model->category ? Link::make($model->category->name)
                            ->route('catalog.product.bycategory', $model->category) : '-'),
                    TD::make('popularity', 'Популярность')
                        ->sort()
                        ->cantHide()
                        ->filter(Input::make()),
                    TD::make('Действия')
                        ->cantHide()
                        ->width('100px')
                        ->render(fn($model) => DropDown::make()
                            ->icon('bs.three-dots-vertical')
                            ->list([
                                Link::make('Редактировать')
                                    ->icon('bs.pencil')
                                    ->route('catalog.product.edit', $model->id),
                                Button::make('Удалить')
                                    ->icon('bs.trash')
                                    ->confirm('Действительно хотите удалить запись?')
                                    ->method('remove', ['id' => $model->id]),
                            ])),
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

    public function remove(int $id): \Illuminate\Http\RedirectResponse
    {
        Product::findOrFail($id)->delete();

        Toast::info('Запись удалена');
        return redirect()->route('catalog.product.list');
    }

    public function resetProductsPopular(): \Illuminate\Http\RedirectResponse
    {
        Product::recalculatePopularity();
        Toast::info('Популярность всех товаров успешно сброшена');
        return redirect()->route('catalog.product.list');
    }
}
