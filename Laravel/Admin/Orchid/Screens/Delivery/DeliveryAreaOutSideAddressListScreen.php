<?php

namespace Admin\Orchid\Screens\Delivery;

use Admin\Orchid\Screens\Permission;
use Orchid\Screen\Components\Cells\DateTime;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class DeliveryAreaOutSideAddressListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     * @return array<string,object>
     */
    public function query(): iterable
    {
        return [
            'datarows' => \Admin\Models\Delivery\DeliveryAreaOutSideAddress::filters()->defaultSort('created_at', 'DESC')->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Адреса вне зоны доставки';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Просмотр запрошенных адресов вне зоны доставки';
    }

    /**
     * The permissions required to access this screen.
     *
     * @return array<string>
     */
    public function permission(): ?iterable
    {
        return [
            Permission::DELIVERY,
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            // Таблица списка моделей
            Layout::table('datarows', [
                TD::make('id', 'ID'),
                TD::make('phone', 'Телефон')->cantHide()->filter(),
                TD::make('address', 'Адрес')->cantHide()->filter(),
                TD::make('comment', 'Комментарий'),
                TD::make('created_at', 'Дата')->sort()->asComponent(DateTime::class),
            ]),
        ];
    }
}
