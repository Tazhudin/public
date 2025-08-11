<?php

namespace Admin\Orchid\Screens\Marketing;

use Admin\Enums\StoryLinkType;
use Admin\Models\Marketing\Story;
use Admin\Orchid\Components\Image;
use Admin\Orchid\Components\ImageThumb;
use Admin\Orchid\Screens\Permission;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class StoryListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,object>
     */
    public function query(): iterable
    {
        return [
            'datarows' => Story::filters()->defaultSort('sort')->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Список Сторисов';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Администрирование сторисов';
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
            Link::make(__('Add'))
                ->icon('bs.plus-circle')
                ->title('Создание сториса')
                ->href(route('marketing.story.create'))
                ->canSee(Auth::user()->hasAccess(Permission::MARKETING_EDIT)),
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
            // Модальное окно для просмотра
            Layout::modal('show-model', Layout::split([
                Layout::legend('model', [
                    Sight::make('name', 'Название'),
                    Sight::make('story_group_id', 'Группа')
                        ->render(fn($model) => $model->storyGroup->name),
                    Sight::make('duration', 'Длительность'),
                    Sight::make('sort', 'Сортировка'),
                    Sight::make('active', 'Активноcть')->render(fn($model) => CheckBox::make()
                        ->disabled(true)
                        ->checked((bool) $model->active)),
                    Sight::make('active_from', 'Начало активности')
                        ->render(fn($model) => $model->active_from?->format('d.m.Y H:i:s')),
                    Sight::make('active_to', 'Окончание активности')
                        ->render(fn($model) => $model->active_to?->format('d.m.Y H:i:s')),
                ]),
                Layout::legend('model', [
                    Sight::make('image', 'Изображение')->asComponent(Image::class),
                    Sight::make('linktype', 'Целевая страница')
                        ->render(fn($model) => $model->linktype ? StoryLinkType::{$model->linktype}->value : '-'),
                    Sight::make('product_id', 'Товар')
                        ->render(fn($model) => $model->product?->name),
                    Sight::make('category_id', 'Категория')
                        ->render(fn($model) => $model->category?->name),
                    Sight::make('selection_id', 'Подборка товаров')
                        ->render(fn($model) => $model->selection?->name),
                ]),
            ]))
                ->title('Просмотр Сториса')
                ->closeButton('Закрыть')
                ->withoutApplyButton()
                ->size(Modal::SIZE_LG)
                ->deferred('loadDataOnOpen'),

            // Таблица списка моделей
            Layout::table('datarows', [
                TD::make('id', 'ID'),
                TD::make('name', 'Название'),
                TD::make('story_group_id', 'Группа')
                    ->render(fn($model) => Link::make($model->storyGroup->name)
                        ->route('marketing.storygroup.edit', $model->storyGroup->id)),
                TD::make('duration', 'Длительность'),
                TD::make('sort', 'Сортировка')->sort(),
                TD::make('active', 'Активность')
                    ->sort()
                    ->render(fn($model) => CheckBox::make()
                        ->disabled(true)
                        ->checked($model->active)),
                TD::make('active_from', 'Начало активности')
                    ->sort()
                    ->render(fn($model) => $model->active_from?->format('d.m.Y')),
                TD::make('active_to', 'Окончание активности')
                    ->sort()
                    ->render(fn($model) => $model->active_to?->format('d.m.Y')),
                TD::make('linktype', 'Целевая страница')
                    ->sort()
                    ->render(fn($model) => $model->linktype ? StoryLinkType::{$model->linktype}->value : ''),
                TD::make('image', 'Изображение')
                    ->asComponent(ImageThumb::class),

                TD::make('Действия')
                    ->cantHide()
                    ->width('100px')
                    ->render(fn($model) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            ModalToggle::make('Посмотреть')
                                ->icon('bs.eye')
                                ->modal('show-model', [
                                    'model' => $model->id
                                ]),
                            Link::make('Редактировать')
                                ->icon('bs.pencil')
                                ->route('marketing.story.edit', $model->id)
                                ->canSee(Auth::user()->hasAccess(Permission::MARKETING_EDIT)),
                            Button::make('Удалить')
                                ->icon('bs.trash')
                                ->confirm('Действительно хотите удалить запись?')
                                ->method('remove', [
                                    'id' => $model->id,
                                ])
                                ->canSee(Auth::user()->hasAccess(Permission::MARKETING_EDIT))
                        ])),
            ]),
        ];
    }

    /**
     * @return array<string, Story>
     */
    public function loadDataOnOpen(\Admin\Models\Marketing\Story $model): iterable
    {
        return [
            'model' => $model
        ];
    }

    public function remove(int $id): \Illuminate\Http\RedirectResponse
    {
        Story::findOrFail($id)->delete();

        Toast::info('Запись удалена');
        return redirect()->route('marketing.story.list');
    }
}
