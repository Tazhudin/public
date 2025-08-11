<?php

namespace Admin\Orchid\Layouts\Delivery;

use Admin\Orchid\Screens\Permission;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class DeliveryAreaListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint
    protected $target = 'datarows';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id', 'ID'),
            TD::make('status', 'Статус'),
            TD::make('is_active', 'Активность')
                ->render(fn($model) => CheckBox::make('is_active')
                    ->disabled()
                    ->checked($model->is_active)),
            TD::make('code', 'Код'),
            TD::make('name', 'Название')->filter(),
            TD::make('Действия')
                ->cantHide()
                ->width('100px')
                ->render(fn($model) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        ModalToggle::make('Смена статуса')
                            ->modal('edit-model', [
                                'model' => $model->id
                            ])
                            ->method('save')
                            ->icon('bs.arrow-repeat')
                            ->canSee(Auth::user()->hasAccess(Permission::DELIVERY_SKLAD)),
                        Link::make('Редактировать')
                            ->icon('bs.pencil')
                            ->route('delivery.area.edit', $model->id)
                            ->canSee(Auth::user()->hasAccess(Permission::DELIVERY_EDIT)),
                        Button::make('Удалить')
                            ->icon('bs.trash')
                            ->confirm('Действительно хотите удалить запись?')
                            ->method('remove', [
                                'id' => $model->id,
                            ])
                            ->canSee(Auth::user()->hasAccess(Permission::DELIVERY_REMOVE))
                    ])),
        ];
    }
}
