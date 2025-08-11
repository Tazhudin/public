<?php

namespace Admin\Orchid\Screens\Marketing;

use Admin\Models\Marketing\Selection;
use Admin\Models\Marketing\Story;
use Admin\Orchid\Screens\Permission;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class SelectionListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,object>
     */
    public function query(): iterable
    {
        return [
            'datarows' => \Admin\Models\Marketing\Selection::filters()->defaultSort('sort')->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Список подборок';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Администрирование подборок';
    }

    /**
     * The permissions required to access this screen.
     *
     * @return iterable|null
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
                ->title('Создание подборки')
                ->href(route('marketing.selections.create'))
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
            Layout::table('datarows', [
                TD::make('id', 'ID'),
                TD::make('name', 'Название'),
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
                TD::make('Действия')
                    ->cantHide()
                    ->width('100px')
                    ->render(fn($model) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make('Редактировать')
                                ->icon('bs.pencil')
                                ->route('marketing.selection.edit', $model->id)
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
    public function loadDataOnOpen(Story $model): iterable
    {
        return [
            'model' => $model
        ];
    }

    public function remove(int $id): \Illuminate\Http\RedirectResponse
    {
        Selection::findOrFail($id)->delete();

        Toast::info('Запись удалена');
        return redirect()->route('marketing.selection.list');
    }
}
