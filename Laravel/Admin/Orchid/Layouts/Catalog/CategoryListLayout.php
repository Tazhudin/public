<?php

namespace Admin\Orchid\Layouts\Catalog;

use Admin\Orchid\Screens\Permission;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class CategoryListLayout extends Table
{
    // phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
    protected $target = 'datarows';

    protected $title = 'Основные категории';

    /**
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('name', 'Наименование')
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn($model) => Link::make($model->name)
                    ->route('catalog.category.list', $model->id)),
            TD::make('code', 'Код категории')
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn($model) => Link::make($model->code)
                    ->route('catalog.category.list', $model->id)),
            TD::make('active', 'Активность')->render(fn($model) => CheckBox::make('active')
                ->disabled(true)
                ->checked((bool) $model->active)),
            TD::make('sort', 'Сортировка')
                ->sort(),
            TD::make('created_at', 'Время создания')
                ->sort()
                ->render(fn($model) => $model->created_at?->format('d.m.Y H:i:s')),
            TD::make('updated_at', 'Время изменения')
                ->sort()
                ->render(fn($model) => $model->updated_at?->format('d.m.Y H:i:s')),

            TD::make('Действия')
                ->cantHide()
                ->width('100px')
                ->render(fn($model) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make('Посмотреть')
                            ->icon('bs.eye')
                            ->route('catalog.category.show', $model->id),
                        Link::make('Редактировать')
                            ->icon('bs.pencil')
                            ->route('catalog.category.edit', $model->id)
                            ->canSee(Auth::user()->hasAccess(Permission::CATALOG_EDIT)),
                        Link::make('Товары категории')
                            ->icon('bs.boxes')
                            ->route('catalog.product.bycategory', $model->id),
                    ])),
        ];
    }

    protected function iconNotFound(): string
    {
        return 'ban';
    }

    protected function textNotFound(): string
    {
        return 'Элементов не найдено';
    }

    protected function subNotFound(): string
    {
        return '';
    }
}
