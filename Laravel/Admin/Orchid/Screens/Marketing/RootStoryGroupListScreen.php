<?php

namespace Admin\Orchid\Screens\Marketing;

use Admin\Models\Marketing\RootStoryGroup;
use Admin\Orchid\Components\Collection;
use Admin\Orchid\Components\Image;
use Admin\Orchid\Components\ImageThumb;
use Admin\Orchid\Screens\Permission;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class RootStoryGroupListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,object>
     */
    public function query(): iterable
    {
        return [
            'datarows' => RootStoryGroup::filters()->defaultSort('sort')->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Список Групп сторисов для главной';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Администрирование групп сторисов для главной';
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
                ->title('Создание группы для главной')
                ->href(route('marketing.rootstorygroup.create'))
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
                    Sight::make('story_group_id', 'Группа')
                        ->render(fn($model) => $model->storyGroup->name),
                    Sight::make('sort', 'Сортировка'),
                    Sight::make('deliveryAreas', 'Зоны доставки')->asComponent(Collection::class),
                    Sight::make('image_desktop', 'Изображение (десктоп)')->asComponent(Image::class),
                ]),
                Layout::legend('model', [
                    Sight::make('image_app', 'Изображение (приложение)')->asComponent(Image::class),
                    Sight::make('image_mobile', 'Изображение (мобильные)')->asComponent(Image::class),
                ]),
            ]))
                ->title('Просмотр Группы для главной')
                ->closeButton('Закрыть')
                ->withoutApplyButton()
                ->size(Modal::SIZE_LG)
                ->deferred('loadDataOnOpen'),

            // Таблица списка моделей
            Layout::table('datarows', [
                TD::make('id', 'ID'),
                TD::make('story_group_id', 'Группа')
                    ->render(fn($model) => Link::make($model->storyGroup->name)
                        ->route('marketing.storygroup.edit', $model->storyGroup->id)),
                TD::make('sort', 'Сортировка')->sort(),
                TD::make('image_app', 'Изображение (приложение)')
                    ->asComponent(ImageThumb::class),
                TD::make('image_mobile', 'Изображение (мобильные)')
                    ->asComponent(ImageThumb::class),
                TD::make('image_desktop', 'Изображение (десктоп)')
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
                                ->route('marketing.rootstorygroup.edit', $model->id)
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
     * @return array<string, RootStoryGroup>
     */
    public function loadDataOnOpen(RootStoryGroup $model): iterable
    {
        return [
            'model' => $model
        ];
    }

    public function remove(int $id): \Illuminate\Http\RedirectResponse
    {
        RootStoryGroup::findOrFail($id)->delete();

        Toast::info('Запись удалена');
        return redirect()->route('marketing.rootstorygroup.list');
    }
}
