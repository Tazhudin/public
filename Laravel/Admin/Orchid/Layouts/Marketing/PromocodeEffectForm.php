<?php

namespace Admin\Orchid\Layouts\Marketing;

use Admin\Models\Marketing\Promocode\Effect\FixedDiscountForProduct;
use Admin\Models\Marketing\Promocode\Effect\FixedDiscountOnOrder;
use Admin\Models\Marketing\Promocode\Effect\FreeDelivery;
use Admin\Models\Marketing\Promocode\Effect\PercentDiscountForProduct;
use Admin\Models\Marketing\Promocode\Effect\PercentDiscountOnOrder;
use Admin\Models\Marketing\Promocode\Effect\PercentDiscountOnProductFromCategory;
use Admin\Models\Marketing\Promocode\Effect\GiftProduct;
use Admin\Orchid\Models\Catalog\CategoryShow;
use Admin\Orchid\Models\Catalog\Product;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Listener;
use Orchid\Screen\Repository;
use Orchid\Support\Facades\Layout;

//phpcs:disable SlevomatCodingStandard
class PromocodeEffectForm extends Listener
{
    protected $targets = [
        'effect',
    ];

    protected function layouts(): iterable
    {
        $effect = $this->query->get('effect') ?? PercentDiscountOnOrder::code();

        return [
            Layout::rows([
                Select::make('effect')
                    ->title('Действие')
                    ->required()
                    ->options($this->promocodeEffects()),
                ...$this->fieldsForEffect($effect)
            ]),
        ];
    }

    public function handle(Repository $repository, Request $request): Repository
    {
        return new Repository([
            'effect' => $request->get('effect'),
        ]);
    }

    private function fieldsForEffect(string $effect): array
    {
        return match ($effect) {
            PercentDiscountOnOrder::code() => $this->fieldsForPercentDiscountOnOrder(),
            PercentDiscountOnProductFromCategory::code() => $this->fieldsForPercentDiscountOnProductFromCategory(),
            FixedDiscountForProduct::code() => $this->fieldsForFixedDiscountForProduct(),
            PercentDiscountForProduct::code() => $this->fieldsForPercentDiscountForProduct(),
            FixedDiscountOnOrder::code() => $this->fieldsForFixedDiscountOnOrder(),
            GiftProduct::code() => $this->fieldsForGiftProduct(),
            FreeDelivery::code() => []
        };
    }

    private function promocodeEffects(): array
    {
        return [
            PercentDiscountOnOrder::code() => 'Процентная скидка на заказ',
            FixedDiscountOnOrder::code() => 'Фиксированная скидка на заказ',
            FixedDiscountForProduct::code() => 'Фиксированная скидка на товар',
            PercentDiscountForProduct::code() => 'Процентная скидка на товар',
            PercentDiscountOnProductFromCategory::code() => 'Процентная скидка на товар из категории',
            GiftProduct::code() => 'Подарочный товар',
            FreeDelivery::code() => 'Бесплатная доставка',
        ];
    }

    private function fieldsForFixedDiscountForProduct(): array
    {
        return [
            Relation::make('product_id')
                ->title('Выберите товар')
                ->fromModel(Product::class, 'name')
                ->searchColumns('name')
                ->required()
                ->chunk(5),
            Input::make('amount')
                ->title('Укажите скидку в рублях')
                ->type('number')
                ->min(1)
                ->required(),
        ];
    }

    private function fieldsForPercentDiscountForProduct(): array
    {
        return [
            Relation::make('product_id')
                ->title('Выберите товар')
                ->fromModel(Product::class, 'name')
                ->searchColumns('name')
                ->required()
                ->chunk(5),
            Input::make('amount')
                ->title('Укажите скидку в процентах')
                ->min(1)
                ->max(99)
                ->type('number')
                ->required(),
        ];
    }

    private function fieldsForFixedDiscountOnOrder(): array
    {
        return [
            Input::make('amount')
                ->title('Укажите скидку в рублях')
                ->type('number')
                ->min(1)
                ->required(),
        ];
    }

    private function fieldsForPercentDiscountOnOrder(): array
    {
        return [
            Input::make('amount')
                ->title('Укажите скидку в процентах')
                ->min(1)
                ->max(99)
                ->type('number')
                ->required(),
            Input::make('max_amount')
                ->title('Укажите максимальную скидку в рублях')
                ->min(1)
                ->type('number')
        ];
    }

    private function fieldsForPercentDiscountOnProductFromCategory(): array
    {
        return [
            Relation::make('category_id')
                ->title('Выберите категорию')
                ->fromModel(CategoryShow::class, 'name')
                ->searchColumns('name')
                ->applyScope('active')
                ->required()
                ->chunk(5),
            Input::make('amount')
                ->title('Укажите скидку в процентах')
                ->min(1)
                ->max(99)
                ->type('number')
                ->required(),
        ];
    }

    private function fieldsForGiftProduct(): array
    {
        return [
            Relation::make('product_id')
                ->title('Выберите товар')
                ->fromModel(Product::class, 'name')
                ->searchColumns('name')
                ->required()
                ->chunk(5),
            Input::make('quantity')
                ->title('Количество')
                ->type('number')
                ->min(1)
                ->required(),
        ];
    }
}
