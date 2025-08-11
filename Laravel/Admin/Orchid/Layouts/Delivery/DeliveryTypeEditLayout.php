<?php

namespace Admin\Orchid\Layouts\Delivery;

use Admin\Enums\DeliveryType as DeliveryTypeEnum;
use Admin\Models\Delivery\DeliveryArea;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class DeliveryTypeEditLayout extends Rows
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint
    protected $title;

    /**
     * Get the fields elements to be displayed.
     *
     * @return Field[]
     */
    protected function fields(): iterable
    {
        return [
            CheckBox::make('model.is_active')
                ->title('Активность')
                ->sendTrueOrFalse(),
            Input::make('model.name')
                ->title('Название')
                ->type('text')
                ->required(),
            Select::make('model.delivery_type')
                ->title('Тип')
                ->options(DeliveryTypeEnum::asArray())
                ->required(),
            Matrix::make('model.price')
                ->title('Стоимость доставки')
                ->addRowLabel('Добавить')
                ->required()
                ->columns([
                    'Стоимость заказа' => 'order_price',
                    'Цена доставки' => 'delivery_price',
                ])->fields([
                    'order_price' => Input::make()
                        ->placeholder('До'),
                    'delivery_price' => Input::make()
                        ->placeholder('Стоимость доставки')
                ]),
            Relation::make('model.deliveryArea')
                ->fromModel(DeliveryArea::class, 'name')
                ->required()
                ->title('Доступен для зон доставки')
        ];
    }
}
