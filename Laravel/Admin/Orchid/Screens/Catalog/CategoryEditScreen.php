<?php

declare(strict_types=1);

namespace Admin\Orchid\Screens\Catalog;

use Admin\Orchid\Models\Catalog\Category;
use Admin\Orchid\Screens\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class CategoryEditScreen extends Screen
{
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint
    public $model;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,object>
     */
    public function query(Category $category): iterable
    {
        return [
            'model' => $category,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Редактирование категории';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Редактирование категории';
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
            // Таблица списка моделей
            Layout::split([
                Layout::rows([
                    Input::make('model.name')
                        ->type('text')
                        ->max(255)
                        ->required()
                        ->title('Наименование')
                        ->placeholder('Наименование'),
                    Input::make('model.code')
                        ->type('text')
                        ->required()
                        ->title('Код для сайта'),
                    Input::make('model.sort')
                        ->type('number')
                        ->required()
                        ->title('Сортировка'),
                    CheckBox::make('model.active')
                        ->sendTrueOrFalse()
                        ->title('Активноcть'),
                ]),
                Layout::legend('model', [
                    Sight::make('id', 'Id категории'),
                    Sight::make('created_at', 'Время создания')
                        ->render(fn($model) => $model->created_at?->format('d.m.Y H:i:s')),
                    Sight::make('updated_at', 'Время изменения')
                        ->render(fn($model) => $model->updated_at?->format('d.m.Y H:i:s')),
                ]),
            ]),
            Layout::split([
                Layout::rows([
                    Picture::make('model.image')
                        ->title('Основное изображение')
                        ->acceptedFiles('.jpg,.png'),
                    Picture::make('model.image_main_page')
                        ->title('Изображение для главной')
                        ->acceptedFiles('.jpg,.png'),
                    Picture::make('model.image_bg_color')
                        ->title('Изображение фона')
                        ->acceptedFiles('.jpg,.png'),
                ]),
                Layout::rows([
                    Picture::make('model.image_desk_large')
                        ->title('Изображение декстоп большое')
                        ->acceptedFiles('.jpg,.png'),
                    Picture::make('model.image_desk_medium')
                        ->title('Изображение декстоп среднее')
                        ->acceptedFiles('.jpg,.png'),
                    Picture::make('model.image_desk_small')
                        ->title('Изображение декстоп малое')
                        ->acceptedFiles('.jpg,.png'),
                ]),
            ]),
            Layout::split([
                Layout::rows([
                    Picture::make('model.image_mob_large')
                        ->title('Изображение мобильные большое')
                        ->acceptedFiles('.jpg,.png'),
                    Picture::make('model.image_mob_small')
                        ->title('Изображение мобильные малое')
                        ->acceptedFiles('.jpg,.png'),
                ]),
                Layout::rows([
                    Picture::make('model.image_app_large')
                        ->title('Изображение приложение большое')
                        ->acceptedFiles('.jpg,.png'),
                    Picture::make('model.image_app_small')
                        ->title('Изображение приложение малое')
                        ->acceptedFiles('.jpg,.png'),
                ]),
            ]),
        ];
    }

    public function save(Category $category, Request $request): RedirectResponse
    {
        $request->validate([
            'model.name' => ['required', 'string', 'max:255'],
            'model.code' => ['required', 'string', 'max:255'],
            'model.sort' => ['required', 'integer'],
            'model.active' => ['boolean'],
        ]);

        $category->fill($request->get('model'));
        $category->save();

        Toast::info('Запись сохранена');

        return $category->parent
            ? redirect()->route('catalog.category.list', $category->parent)
            : redirect()->route('catalog.category.root');
    }

    public function remove(Category $category): RedirectResponse
    {
        $category->delete();

        Toast::info('Запись удалена');
        return $category->parent
            ? redirect()->route('catalog.category.list', $category->parent)
            : redirect()->route('catalog.category.root');
    }
}
