<?php

namespace Admin\Orchid\Screens\Marketing;

use Admin\Models\Marketing\StoryGroup;
use Admin\Orchid\Screens\Permission;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class StoryGroupEditScreen extends Screen
{
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint
    public $model;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,object>
     */
    public function query(StoryGroup $storygroup): iterable
    {
        return [
            'model' => $storygroup,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Редактирование Группы сторисов';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Создание и редактирование групп сторисов';
    }

    /**
     * The permissions required to access this screen.
     *
     * @return array<string>
     */
    public function permission(): ?iterable
    {
        return [
            Permission::MARKETING_READ,
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
                Input::make('model.code')
                    ->title('Код группы')
                    ->type('text')
                    ->max(100)
                    ->required(),
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
            ]),
        ];
    }

    public function save(StoryGroup $storygroup, Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'model.name' => ['required', 'string', 'max:100'],
            'model.code' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($storygroup) {
                    $exists = StoryGroup::whereRaw('UPPER(code) = ?', [strtoupper($value)])
                        ->where('id', '!=', $storygroup->id)
                        ->exists();

                    if ($exists) {
                        $fail('Значение "Код группы" уже существует.');
                    }
                },
            ],
            'model.sort' => ['required', 'integer'],
            'model.active' => ['boolean'],
            'model.active_from' => ['date', 'nullable'],
            'model.active_to' => ['date', 'nullable'],
        ]);

        $data = $request->get('model');
        $data['code'] = strtoupper($data['code']);
        $storygroup->fill($data);
        $storygroup->save();

        Toast::info('Запись сохранена');
        return redirect()->route('marketing.storygroup.list');
    }

    public function remove(\Admin\Models\Marketing\StoryGroup $storygroup): \Illuminate\Http\RedirectResponse
    {
        $storygroup->delete();

        Toast::info('Запись удалена');
        return redirect()->route('marketing.storygroup.list');
    }
}
