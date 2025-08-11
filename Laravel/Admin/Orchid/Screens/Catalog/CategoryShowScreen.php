<?php

declare(strict_types=1);

namespace Admin\Orchid\Screens\Catalog;

use Admin\Orchid\Components\Image;
use Admin\Orchid\Models\Catalog\Category;
use Admin\Orchid\Screens\Permission;
use Orchid\Screen\Action;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Layout;

class CategoryShowScreen extends Screen
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
        return 'Просмотр категории';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Просмотр категорий';
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
                Layout::legend('model', [
                    Sight::make('name', 'Наименование'),
                    Sight::make('sort', 'Сортировка'),
                    Sight::make('code', 'Код для сайта'),
                    Sight::make('active', 'Активноcть')->render(fn($model) => CheckBox::make('active')
                        ->disabled(true)
                        ->checked((bool) $model->active)),
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
                Layout::legend('model', [
                    Sight::make('image', 'Основное изображение')->asComponent(Image::class),
                    Sight::make('image_main_page', 'Изображение для главной')->asComponent(Image::class),
                    Sight::make('image_bg_color', 'Изображение фона')->asComponent(Image::class),
                ]),
                Layout::legend('model', [
                    Sight::make('image_desk_large', 'Изображение декстоп большое')->asComponent(Image::class),
                    Sight::make('image_desk_medium', 'Изображение декстоп среднее')->asComponent(Image::class),
                    Sight::make('image_desk_small', 'Изображение декстоп малое')->asComponent(Image::class),
                ]),
            ]),
            Layout::split([
                Layout::legend('model', [
                    Sight::make('image_mob_large', 'Изображение мобильные большое')->asComponent(Image::class),
                    Sight::make('image_mob_small', 'Изображение мобильные малое')->asComponent(Image::class),
                ]),
                Layout::legend('model', [
                    Sight::make('image_app_large', 'Изображение приложение большое')->asComponent(Image::class),
                    Sight::make('image_app_small', 'Изображение приложение малое')->asComponent(Image::class),
                ]),
            ]),
        ];
    }
}
