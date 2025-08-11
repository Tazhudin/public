<?php

namespace Admin\Orchid\Layouts\Catalog;

use Admin\Orchid\Components\Image;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ProductImagesLayout extends Table
{
    // phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
    protected $target = 'images';

    protected $title = 'Изображения товара';

    /**
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('is_main', 'Основное')->render(fn($model) => CheckBox::make('is_main')
                ->disabled(true)
                ->checked((bool) $model->is_main)),
            TD::make('url', 'Изображение')->asComponent(Image::class),
        ];
    }

    protected function iconNotFound(): string
    {
        return 'ban';
    }

    protected function textNotFound(): string
    {
        return 'Изображений не найдено';
    }

    protected function subNotFound(): string
    {
        return '';
    }
}
