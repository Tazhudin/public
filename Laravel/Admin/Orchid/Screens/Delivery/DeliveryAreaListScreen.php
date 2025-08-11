<?php

namespace Admin\Orchid\Screens\Delivery;

use Admin\Models\Delivery\DeliveryArea;
use Admin\Orchid\Screens\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Library\ValueObject\DeliveryAreaStatus;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class DeliveryAreaListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     * @return array<string,object>
     */
    public function query(): iterable
    {
        return [
            'datarows' => DeliveryArea::filters()->defaultSort('name')->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Зоны доставки';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Администрирование зон доставок';
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
                ->title('Создание зоны доставки')
                ->href(route('delivery.area.create'))
                ->canSee(Auth::user()->hasAccess(Permission::DELIVERY_EDIT)),
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
            // Модальное окно для модели
            Layout::modal('edit-model', Layout::rows([
                Select::make('model.status')
                    ->title('Статус')
                    ->required()
                    ->options(
                        collect(DeliveryAreaStatus::cases())->mapWithKeys(fn(DeliveryAreaStatus $status) => [
                            $status->value => $status->getName()
                        ])->toArray()
                    ),
            ]))
                ->title('Смена статуса зоны доставки')
                ->closeButton('Закрыть')
                ->applyButton('Сохранить')
                ->method('save')
                ->size(Modal::SIZE_LG)
                ->deferred('loadDataOnOpen'),

            \Admin\Orchid\Layouts\Delivery\DeliveryAreaListLayout::class,
        ];
    }

    /**
     * Получить данные модели
     *
     * @return array<string,Faq>
     */
    public function loadDataOnOpen(DeliveryArea $model): iterable
    {
        return [
            'model' => $model
        ];
    }

    /**
     * Сохранить
     */
    public function save(DeliveryArea $model, Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'model.status' => ['required', 'string'],
        ]);

        $model->fill($request->collect('model')->toArray());
        $model->save();

        Toast::info('Запись сохранена');
        return redirect()->route('delivery.area.list');
    }

    public function remove(string $id): \Illuminate\Http\RedirectResponse
    {
        $area = DeliveryArea::findOrFail($id);
        if (DeliveryArea::DEMO_ZONE_CODE == $area->code) {
            Toast::info('Удаление запрещено. Зона "Демо" не может быть удалена');
            return redirect()->route('delivery.area.list');
        }

        $area->delete();

        Toast::info('Зона доставки удалена');
        return redirect()->route('delivery.area.list');
    }
}
