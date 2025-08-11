<?php

namespace Admin\Orchid\Layouts\Catalog;

use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ProductPricesLayout extends Table
{
    // phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
    protected $target = 'prices';

    protected $title = 'Цены и остатки';

    /**
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('stockrel.name', 'Склад'),
            TD::make('balance', 'Остаток')
                ->alignRight()
                ->render(fn($model) => ModalToggle::make($model->stock)
                    ->modal('edit-stock', [
                        'model' => $model->stockvalue_id
                    ])
                    ->method('saveStock')
                    ->icon('bs.pencil')),
            TD::make('price', 'Цена')
                ->alignRight()
                ->render(fn($model) => ModalToggle::make($model->price)
                    ->modal('edit-price', [
                        'model' => $model->pricevalue_id
                    ])
                    ->method('savePrice')
                    ->icon('bs.pencil')),
            TD::make('discount_price', 'Скидка')
                ->alignRight()
                ->render(fn($model) => ModalToggle::make($model->discount_price)
                    ->modal('edit-price', [
                        'model' => $model->pricevalue_id
                    ])
                    ->method('savePrice')
                    ->icon('bs.pencil')),
            TD::make('available_amount', 'Доступное количество')
                ->alignRight(),
        ];
    }

    protected function iconNotFound(): string
    {
        return 'ban';
    }

    protected function textNotFound(): string
    {
        return 'Данных на складах не найдено';
    }

    protected function subNotFound(): string
    {
        return '';
    }
}
