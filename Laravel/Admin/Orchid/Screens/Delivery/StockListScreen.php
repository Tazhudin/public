<?php

namespace Admin\Orchid\Screens\Delivery;

use Admin\Orchid\Screens\Permission;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class StockListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     * @return array<string,object>
     */
    public function query(): iterable
    {
        return [
            'datarows' => \Admin\Models\Delivery\Store::filters()->defaultSort('created_at', 'DESC')->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Склады';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Просмотр складов';
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
            Layout::table('datarows', [
                TD::make('id', 'ID'),
                TD::make('name', 'Название')
                    ->sort()
                    ->filter(Input::make()),
                TD::make('created_at', 'Дата создания')
                    ->sort()
                    ->render(fn($model) => $model->created_at->format('d.m.Y H:i:s')),
            ]),
        ];
    }
}
