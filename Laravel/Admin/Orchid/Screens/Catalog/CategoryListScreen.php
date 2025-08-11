<?php

declare(strict_types=1);

namespace Admin\Orchid\Screens\Catalog;

use Admin\Orchid\Layouts\Catalog\CategoryDuplicateListLayout;
use Admin\Orchid\Layouts\Catalog\CategoryListLayout;
use Admin\Orchid\Models\Catalog\Category;
use Admin\Orchid\Models\Catalog\CategoryShow;
use Admin\Orchid\Screens\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class CategoryListScreen extends Screen
{
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint
    public $category;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,object>
     */
    public function query(Category $category): iterable
    {
        return [
            'category' => $category,
            'datarows' => $category->exists
                ? Category::where('parent_id', $category->id)->filters()->defaultSort('sort')->paginate(10)
                : Category::whereNull('parent_id')->filters()->defaultSort('sort')->paginate(10),
            'duplicates' => $category->exists
                ? Category::whereHas('parentCategories', function (Builder $query) use ($category): void {
                    $query->where('catalog__category_to_category.parent_id', $category->id);
                })->filters()->defaultSort('sort')->paginate(10)
                : []
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Список категорий';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Просмотр дерева категорий';
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
                ->modal('make-duplicate')
                ->title('Добавить дублирующую категорию')
                ->method('makeDuplicate')
                ->icon('bs.plus-circle')
                ->canSee($this->category->exists && Auth::user()->hasAccess(Permission::CATALOG_EDIT)),
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
            Layout::modal('make-duplicate', Layout::rows([
                Relation::make('category_id')
                    ->title('Категория')
                    ->fromModel(CategoryShow::class, 'name')
                    ->applyScope('active')
                    ->searchColumns('name')
                    ->chunk(50),
            ]))
                ->title('Добавить связь дублирующей подкатегории')
                ->closeButton('Закрыть')
                ->applyButton('Сохранить'),
            \Admin\Orchid\Layouts\Catalog\CatalogCategoryFiltersLayout::class,
            CategoryListLayout::class,
            CategoryDuplicateListLayout::class,
        ];
    }

    public function makeDuplicate(Category $category, Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'category_id' => ['required', 'uuid'],
        ]);
        Category::find($request->category_id)->parentCategories()->attach([$category->id]);

        Toast::info('Связь сохранена');
        return redirect()->route('catalog.category.show', $category);
    }

    public function removeDuplicate(Category $category, string $category_id): \Illuminate\Http\RedirectResponse
    {
        Category::find($category_id)->parentCategories()->detach([$category->id]);

        Toast::info('Связь удалена');
        return redirect()->route('catalog.category.show', $category);
    }
}
