<?php

namespace Admin\Orchid\Layouts\Marketing;

use Admin\Orchid\Models\Customer\Customer;
use Admin\Models\Marketing\Promocode\Constraint\AmountOfUsageConstraint;
use Admin\Models\Marketing\Promocode\Constraint\AmountOfUsagePerCustomerConstraint;
use Admin\Models\Marketing\Promocode\Constraint\ListOfCustomerConstraint;
use Admin\Models\Marketing\Promocode\Constraint\StockConstraint;
use Admin\Models\Marketing\Promocode\Constraint\WeekDayConstraint;
use Admin\Models\PriceAndStock\Stock;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Listener;
use Orchid\Screen\Repository;
use Orchid\Support\Facades\Layout;

//phpcs:disable SlevomatCodingStandard
class PromocodeConstraintForm extends Listener
{
    protected $targets = [
        'constraint',
    ];

    protected function layouts(): iterable
    {
        $constraint = $this->query->get('constraint') ?? AmountOfUsageConstraint::code();

        return [
            Layout::rows([
                Select::make('constraint')
                    ->required()
                    ->title('Ограничение')
                    ->options($this->promocodeConstraints()),
                ...$this->fieldsForConstraint($constraint)
            ]),
        ];
    }

    public function handle(Repository $repository, Request $request): Repository
    {
        return new Repository([
            'constraint' => $request->get('constraint'),
        ]);
    }

    private function promocodeConstraints(): array
    {
        return [
            AmountOfUsageConstraint::code() => 'Количество использований',
            AmountOfUsagePerCustomerConstraint::code() => 'Количество использований на пользователя',
            ListOfCustomerConstraint::code() => 'Список покупателей',
            StockConstraint::code() => 'По складам',
            WeekDayConstraint::code() => 'По дням недели',
        ];
    }

    private function fieldsForConstraint(string $constraint): array
    {
        return match ($constraint) {
            AmountOfUsageConstraint::code(), AmountOfUsagePerCustomerConstraint::code() => [
                Input::make('amount')
                    ->type('number')
                    ->required()
                    ->title('Введите количество использований'),
            ],
            ListOfCustomerConstraint::code() => [
                Relation::make('phone_numbers')
                    ->title('Выберите покупатей(Номера телефонов)')
                    ->fromModel(Customer::class, 'phone_number', 'phone_number')
                    ->searchColumns('phone_number')
                    ->required()
                    ->multiple()
                    ->chunk(5),
            ],
            StockConstraint::code() => [
                Relation::make('stock_ids')
                    ->title('Выберите склады')
                    ->fromModel(Stock::class, 'name')
                    ->searchColumns('name')
                    ->multiple()
                    ->required()
                    ->chunk(5)
            ],
            WeekDayConstraint::code() => [
                Select::make('weekday_numbers')
                    ->title('Выберите дни недели')
                    ->options([
                        '1' => 'Понедельник',
                        '2' => 'Вторник',
                        '3' => 'Среда',
                        '4' => 'Четверг',
                        '5' => 'Пятница',
                        '6' => 'Суббота',
                        '7' => 'Воскресенье',
                    ])->multiple()
                    ->required(),
            ],
        };
    }
}
