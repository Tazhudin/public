<?php

namespace Admin\Orchid\Screens\Marketing;

use Admin\Enums\StoryLinkType;
use Admin\Models\Marketing\Story;
use Admin\Models\Marketing\StoryGroup;
use Admin\Orchid\Models\Catalog\Category;
use Admin\Orchid\Models\Catalog\Product;
use Admin\Orchid\Screens\Permission;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class StoryEditScreen extends Screen
{
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint
    public $model;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,object>
     */
    public function query(Story $story): iterable
    {
        return [
            'model' => $story,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Редактирование Сториса';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Создание и редактирование сторисов';
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
                Select::make('model.story_group_id')
                    ->title('Группа')
                    ->fromModel(StoryGroup::class, 'name'),
                Input::make('model.duration')
                    ->title('Длительность')
                    ->type('number')
                    ->value(10)
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
                Picture::make('model.image')
                    ->title('Изображение')
                    ->acceptedFiles('.jpg,.png'),
                Select::make('model.linktype')
                    ->title('Целевая страница')
                    ->options(['' => 'Не выбрано'] + StoryLinkType::asArray()),
                Relation::make('model.product_id')
                    ->title('Товар')
                    ->fromModel(Product::class, 'name')
                    ->allowEmpty(),
                Relation::make('model.category_id')
                    ->title('Категория')
                    ->fromModel(Category::class, 'name')
                    ->allowEmpty(),
                Relation::make('model.selection_id')
                    ->title('Подборка товаров')
                    ->fromModel(\Admin\Models\Marketing\Selection::class, 'name')
                    ->allowEmpty(),
                Input::make('model.weblink')
                    ->title('Веб-ресурс')
                    ->type('text')
                    ->max(255),
            ]),
        ];
    }

    public function save(Story $story, Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'model.name' => ['required', 'string', 'max:100'],
            'model.duration' => ['required', 'integer'],
            'model.sort' => ['required', 'integer'],
            'model.active' => ['boolean'],
            'model.active_from' => ['date', 'nullable'],
            'model.active_to' => ['date', 'nullable'],
            'model.product_id' =>
                ['required_if:model.linktype,PRODUCT', 'prohibited_unless:model.linktype,PRODUCT'],
            'model.category_id' =>
                ['required_if:model.linktype,CATEGORY', 'prohibited_unless:model.linktype,CATEGORY'],
            'model.selection_id' =>
                ['required_if:model.linktype,SELECTION', 'prohibited_unless:model.linktype,SELECTION'],
            'model.weblink' =>
                ['required_if:model.linktype,WEBLINK', 'prohibited_unless:model.linktype,WEBLINK'],
        ]);

        $story->fill($request->get('model'));
        $story->save();

        Toast::info('Запись сохранена');
        return redirect()->route('marketing.story.list');
    }

    public function remove(Story $story): \Illuminate\Http\RedirectResponse
    {
        $story->delete();

        Toast::info('Запись удалена');
        return redirect()->route('marketing.story.list');
    }
}
