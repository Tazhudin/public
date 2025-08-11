<?php

namespace Admin\Orchid\Screens\Delivery;

use Admin\Enums\DeliveryType as DeliveryTypeEnum;
use Admin\Models\Delivery\DeliveryType;
use Admin\Orchid\Screens\Permission;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Order\Infrastructure\Repository\PromocodeRepository;

class DeliveryTypeListScreen extends Screen
{
    public function __construct(protected PromocodeRepository $api)
    {
    }

    /**
     * Fetch data to be displayed on the screen.
     * @return array<string,object>
     */
    public function query(): iterable
    {
        return [
            'datarows' => DeliveryType::filters()->defaultSort('is_active', 'DESC')->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Типы доставки';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Администрирование типов доставок';
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
            Link::make(__('Add'))
                ->icon('bs.plus-circle')
                ->title('Создание типа доставки')
                ->href(route('delivery.type.create')),
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
                TD::make('is_active', 'Активность')
                    ->sort()
                    ->render(fn($model) => CheckBox::make('is_active')
                        ->disabled(true)
                        ->checked($model->is_active)),
                TD::make('name', 'Название')->filter()->sort(),
                TD::make('delivery_type', 'Тип')
                    ->sort()
                    ->filter(Select::make()
                        ->options(['' => 'Все'] + DeliveryTypeEnum::asArray()))
                    ->render(fn($model) =>  DeliveryTypeEnum::{$model->delivery_type}->value),
                TD::make('Действия')
                    ->cantHide()
                    ->width('100px')
                    ->render(fn($model) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make('Редактировать')
                                ->icon('bs.pencil')
                                ->route('delivery.type.edit', $model->id),
                            Button::make('Удалить')
                                ->icon('bs.trash')
                                ->confirm('Действительно хотите удалить запись?')
                                ->method('remove', [
                                    'id' => $model->id,
                                ])
                        ])),
            ]),
        ];
    }


    public function remove(string $id): \Illuminate\Http\RedirectResponse
    {
        \Admin\Models\Delivery\DeliveryType::findOrFail($id)->delete();

        Toast::info('Запись удалена');
        return redirect()->route('delivery.type.list');
    }
}
