<?php

namespace Admin\Orchid\Screens\Marketing;

use Admin\Models\Delivery\DeliveryArea;
use Admin\Models\Marketing\RootStoryGroup;
use Admin\Models\Marketing\StoryGroup;
use Admin\Orchid\Screens\Permission;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class RootStoryGroupEditScreen extends Screen
{
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint
    public $model;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,object>
     */
    public function query(RootStoryGroup $rootstorygroup): iterable
    {
        return [
            'model' => $rootstorygroup,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Редактирование Группы сторисов для главной';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Создание и редактирование групп сторисов для главной';
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
                Select::make('model.story_group_id')
                    ->title('Группа')
                    ->fromModel(StoryGroup::class, 'name')
                    ->required(),
                Input::make('model.sort')
                    ->title('Сортировка')
                    ->type('number')
                    ->value(500)
                    ->required(),
                Relation::make('model.deliveryAreas')
                    ->title('Зоны доставки')
                    ->fromModel(DeliveryArea::class, 'name')
                    ->multiple()
                    ->allowEmpty(),
                Picture::make('model.image_app')
                    ->title('Изображение (приложение)')
                    ->acceptedFiles('.jpg,.png'),
                Picture::make('model.image_mobile')
                    ->title('Изображение (мобильные)')
                    ->acceptedFiles('.jpg,.png'),
                Picture::make('model.image_desktop')
                    ->title('Изображение (десктоп)')
                    ->acceptedFiles('.jpg,.png'),
            ]),
        ];
    }

    public function save(RootStoryGroup $rootstorygroup, Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'model.story_group_id' => ['required', 'integer'],
            'model.sort' => ['required', 'integer'],
        ]);

        $rootstorygroup->fill($request->get('model'));
        $rootstorygroup->save();
        $rootstorygroup->deliveryAreas()->sync($request->get('model')['deliveryAreas'] ?? []);

        Toast::info('Запись сохранена');
        return redirect()->route('marketing.rootstorygroup.list');
    }

    public function remove(\Admin\Models\Marketing\RootStoryGroup $rootstorygroup): \Illuminate\Http\RedirectResponse
    {
        $rootstorygroup->delete();

        Toast::info('Запись удалена');
        return redirect()->route('marketing.rootstorygroup.list');
    }
}
