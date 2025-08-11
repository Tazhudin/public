<?php

namespace Admin\Orchid\Screens\Marketing;

use Admin\Models\Delivery\DeliveryArea;
use Admin\Models\Selection;
use Admin\Orchid\Models\Catalog\Product;
use Admin\Orchid\Screens\Permission;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class SelectionEditScreen extends Screen
{
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint
    public $model;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,object>
     */
    public function query(Selection $selection): iterable
    {
        $selection->load(['products', 'deliveryAreas']);

        return [
            'model' => $selection,
            'model.delivery_area' => $selection->deliveryAreas->pluck('id')->toArray(),
            'model.products' => $selection->products->pluck('id')->toArray(),
        ];
    }


    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Редактирование подборки';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Создание и редактирование подборки';
    }

    /**
     * The permissions required to access this screen.
     *
     * @return array<string>
     */
    public function permission(): ?iterable
    {
        return [
            Permission::MARKETING_EDIT,
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
            Button::make(__('Save'))
                ->icon('bs.check-circle')
                ->method('save'),

            Button::make(__('Remove'))
                ->icon('bs.trash3')
                ->confirm('Действительно хотите удалить запись?')
                ->method('remove')
                ->canSee($this->model->exists),
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
            Layout::rows([
                Input::make('model.name')
                    ->title('Название')
                    ->type('text')
                    ->max(100)
                    ->required(),
                Relation::make('model.delivery_area')
                    ->title('Зона доставки')
                    ->multiple()
                    ->fromModel(DeliveryArea::class, 'name'),
                Input::make('model.sort')
                    ->title('Сортировка')
                    ->type('number')
                    ->value(500)
                    ->required(),
                CheckBox::make('model.active')
                    ->title('Активность')
                    ->sendTrueOrFalse(),
                DateTimer::make('model.active_from')
                    ->title('Начало активности')
                    ->format('d.m.Y')
                    ->serverFormat()
                    ->allowEmpty(),
                DateTimer::make('model.active_to')
                    ->title('Окончание активности')
                    ->format('d.m.Y')
                    ->serverFormat()
                    ->allowEmpty(),
                Picture::make('model.image')
                    ->title('Изображение')
                    ->acceptedFiles('.jpg,.png'),
                Relation::make('model.products')
                    ->title('Товары')
                    ->fromModel(Product::class, 'name')
                    ->multiple(),
            ]),
        ];
    }

    public function save(Selection $selection, Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'model.name' => ['required', 'string', 'max:100'],
            'model.sort' => ['required', 'integer'],
            'model.active' => ['boolean'],
            'model.active_from' => ['date', 'nullable'],
            'model.active_to' => ['date', 'nullable'],
            'model.image' => ['required', 'string'],
            'model.delivery_area' => ['required', 'array'],
            'model.products' => ['required', 'array'],
        ]);

        $selection->fill($validated['model']);
        $selection->save();

        if (isset($validated['model']['delivery_area'])) {
            $selection->deliveryAreas()->sync($validated['model']['delivery_area']);
        }

        if (isset($validated['model']['products'])) {
            $selection->products()->sync($validated['model']['products']);
        }

        Toast::info('Запись сохранена');
        return redirect()->route('marketing.selection.list');
    }


    public function remove(Selection $selection): \Illuminate\Http\RedirectResponse
    {
        $selection->delete();

        Toast::info('Запись удалена');
        return redirect()->route('marketing.selection.list');
    }
}
