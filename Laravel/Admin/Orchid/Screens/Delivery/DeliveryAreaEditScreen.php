<?php

namespace Admin\Orchid\Screens\Delivery;

use Admin\Models\Delivery\DeliveryArea;
use Admin\Models\Delivery\Store;
use Admin\Orchid\Rules\Delivery\CoordinateValidator;
use Admin\Orchid\Rules\Delivery\WorkTimeValidation;
use Admin\Orchid\Screens\Permission;
use Illuminate\Http\Request;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Code;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class DeliveryAreaEditScreen extends Screen
{
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint
    public $model;

    /**
     * The permissions required to access this screen.
     * @return iterable<string>|null
     */
    public function permission(): ?iterable
    {
        return [
            Permission::DELIVERY_EDIT,
        ];
    }


    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,mixed>
     */
    public function query(DeliveryArea $area): iterable
    {
        if ($area->exists) {
            $workTime = json_decode($area->work_time, true) ?? [];
            $area->work_time = $workTime;
        }

        return [
            'model' => $area
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Редактирование зоны доставки';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Создание и редактирование зоны доставки';
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Save'))
                ->icon('check-circle')
                ->method('save'),

            Button::make(__('Remove'))
                ->icon('trash')
                ->method('remove')
                ->canSee($this->model->exists),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return array<mixed>
     */
    public function layout(): iterable
    {
        $workTimeData = $this->model->work_time ?? [];

        $daysOfWeek = [
            'ПН' => 'Понедельник',
            'ВТ' => 'Вторник',
            'СР' => 'Среда',
            'ЧТ' => 'Четверг',
            'ПТ' => 'Пятница',
            'СБ' => 'Суббота',
            'ВС' => 'Воскресенье',
        ];

        foreach ($daysOfWeek as $key => $label) {
            $fields[] = Group::make([
                Input::make("model.work_time.{$key}.time")
                    ->title($label)
                    ->value($workTimeData[$key] ?? '')
                    ->placeholder('09:00-21:00')
                    ->required()
                    ->horizontal()
            ]);
        }

        return [
            Layout::columns([
                [
                    Layout::rows([
                        Input::make('model.code')
                            ->title('Код')
                            ->type('text')
                            ->max(10)
                            ->required(),

                        Input::make('model.name')
                            ->title('Название')
                            ->type('text')
                            ->required(),

                        Input::make('model.delivery_time')
                            ->title('Время доставки')
                            ->type('number')
                            ->required(),

                        Select::make('model.stock_id')
                            ->title('Склад')
                            ->fromModel(Store::class, 'name')
                            ->required(),

                        Group::make([
                            CheckBox::make('model.is_active')
                                ->title('Активность')
                                ->sendTrueOrFalse(),
                        ])->fullWidth()
                    ]),
                ],
                Layout::rows($fields)
            ]),
            Layout::rows([
                Code::make('model.coordinates')
                    ->title('Координаты')
                    ->language('string')
            ])
        ];
    }

    /**
     * Сохранение данных зоны доставки.
     */
    public function save(DeliveryArea $area, Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'model.code' => ['required', 'string', 'max:100'],
            'model.name' => ['required', 'string'],
            'model.delivery_time' => ['required', 'integer', 'gt:0'],
            'model.is_active' => ['boolean'],
            'model.coordinates' => ['required', 'string', new CoordinateValidator()],
            'model.work_time' => ['required', 'array', new WorkTimeValidation()],
        ]);

        $model = $request->get('model');

        $workTime = [];
        foreach ($model['work_time'] as $day => $dateTime) {
            if (!empty($dateTime['time'])) {
                $workTime[$day] = $dateTime['time'];
            }
        }
        $model['work_time'] = json_encode($workTime);

        $area->fill($model);
        $area->save();

        Toast::info('Зона доставки успешно сохранена.');

        return redirect()->route('delivery.area.edit', $area->id);
    }


    /**
     * Удаление зоны доставки.
     */
    public function remove(DeliveryArea $area): \Illuminate\Http\RedirectResponse
    {
        if ($area->code === 'DEMO') {
            Toast::info('Зона "Демо" не может быть удалена.');
            return redirect()->route('delivery.area.list');
        }

        $area->delete();

        Toast::info('Зона доставки удалена.');
        return redirect()->route('delivery.area.list');
    }
}
