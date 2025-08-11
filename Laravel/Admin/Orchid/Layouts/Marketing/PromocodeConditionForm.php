<?php

namespace Admin\Orchid\Layouts\Marketing;

use Admin\Models\Marketing\Promocode\Condition\BasketItemsQuantityCondition;
use Admin\Models\Marketing\Promocode\Condition\BasketProductsAmountCondition;
use Admin\Models\Marketing\Promocode\Condition\ListOfProductsCondition;
use Admin\Models\Marketing\Promocode\Condition\NumberOfCustomerOrdersCondition;
use Admin\Models\Marketing\Promocode\Condition\ProductsCategoryCondition;
use Admin\Orchid\Models\Catalog\CategoryShow;
use Admin\Orchid\Models\Catalog\Product;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Listener;
use Orchid\Screen\Repository;
use Orchid\Support\Facades\Layout;

//phpcs:disable SlevomatCodingStandard
class PromocodeConditionForm extends Listener
{
    protected $targets = [
        'condition',
    ];

    protected function layouts(): iterable
    {
        $condition = $this->query->get('condition') ?? BasketItemsQuantityCondition::code();

        return [
            Layout::rows([
                Select::make('condition')
                    ->required()
                    ->title('Условие')
                    ->options($this->promocodeConditions()),
                ...$this->fieldsForCondition($condition)
            ]),
        ];
    }

    public function handle(Repository $repository, Request $request): Repository
    {
        return new Repository([
            'condition' => $request->get('condition'),
        ]);
    }

    private function promocodeConditions(): array
    {
        return [
            BasketItemsQuantityCondition::code() => 'Количество позиций в корзине',
            BasketProductsAmountCondition::code() => 'Сумма товаров в корзине',
            ListOfProductsCondition::code() => 'Список товаров',
            ProductsCategoryCondition::code() => 'Товар из категории',
            NumberOfCustomerOrdersCondition::code() => 'Количество оплаченных заказов покупателя',
        ];
    }

    private function fieldsForCondition(string $condition): array
    {
        return match ($condition) {
            ListOfProductsCondition::code() => $this->fieldsForListOfProductsCondition(),
            BasketItemsQuantityCondition::code() => $this->fieldsForBasketItemsQuantityCondition(),
            BasketProductsAmountCondition::code() => $this->fieldsForBasketProductsAmountCondition(),
            ProductsCategoryCondition::code() => $this->fieldsForProductsCategoryCondition(),
            NumberOfCustomerOrdersCondition::code() => $this->fieldsForNumberOfCustomerOrdersCondition(),
        };
    }

    private function fieldsForBasketItemsQuantityCondition(): array
    {
        return [
            Input::make('items_quantity')
                ->title('Укажите необходимое количество позиций')
                ->required()
                ->type('number')
        ];
    }

    private function fieldsForBasketProductsAmountCondition(): array
    {
        return [
            Input::make('products_amount')
                ->title('Укажите необходимую сумму')
                ->required()
                ->type('number')
        ];
    }

    private function fieldsForListOfProductsCondition(): array
    {
        return [
            Relation::make('products_ids')
                ->title('Выберите товары')
                ->fromModel(Product::class, 'name')
                ->searchColumns('name')
                ->required()
                ->multiple()
                ->chunk(5),
        ];
    }

    private function fieldsForProductsCategoryCondition(): array
    {
        return [
            Relation::make('category_id')
                ->title('Выберите категорию')
                ->fromModel(CategoryShow::class, 'name')
                ->searchColumns('name')
                ->required()
                ->chunk(5),
        ];
    }

    private function fieldsForNumberOfCustomerOrdersCondition(): array
    {
        return [
            Group::make([
                Select::make('compare')
                    ->title('Условие')
                    ->required()
                    ->options([
                        'greater' => 'Больше',
                        'less' => 'Меньше',
                    ]),
                Input::make('orders_count')
                    ->title('Укажите количество заказов')
                    ->required()
                    ->type('number')
            ])
        ];
    }
}
