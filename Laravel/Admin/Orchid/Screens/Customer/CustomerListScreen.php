<?php

namespace Admin\Orchid\Screens\Customer;

use Admin\Orchid\Models\Customer\Customer;
use Admin\Orchid\Screens\Permission;
use Library\ValueObject\Phone;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Color;

class CustomerListScreen extends Screen
{
    /**
     * @return array<string, mixed>
     */
    public function query(): iterable
    {
        return [
            'customers' => Customer::filters()->paginate(10),
        ];
    }

    public function name(): ?string
    {
        return 'Покупатели';
    }

    /**
     * @return string[]|null
     */
    public function permission(): ?iterable
    {
        return [Permission::CUSTOMER];
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            \Orchid\Support\Facades\Layout::table('customers', [
                TD::make('full_name', 'ФИО')->render(function (Customer $customer) {
                    return Link::make($customer->getDisplayDescription())
                        ->type(Color::BASIC)
                        ->route('customer.detail', $customer->id);
                }),
                TD::make('phone_number', 'Номер телефона')->render(function (Customer $customer) {
                    return Phone::fromString($customer->phone_number)->formattedString();
                })->filter(),
            ])
        ];
    }
}
