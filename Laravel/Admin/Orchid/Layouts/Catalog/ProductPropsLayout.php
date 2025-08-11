<?php

namespace Admin\Orchid\Layouts\Catalog;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ProductPropsLayout extends Table
{
    // phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
    protected $target = 'props';

    protected $title = 'Свойства товара';

    /**
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('property.name', 'Свойство'),
            TD::make('value', 'Значение')
                ->render(function ($propertyValue): string {
                    $value = $propertyValue->value;
                    return $propertyValue->enumValue?->name ?? $value;
                }),
        ];
    }

    protected function iconNotFound(): string
    {
        return 'ban';
    }

    protected function textNotFound(): string
    {
        return 'Свойств товара не найдено';
    }

    protected function subNotFound(): string
    {
        return '';
    }
}
