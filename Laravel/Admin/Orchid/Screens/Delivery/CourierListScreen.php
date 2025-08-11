<?php

namespace Admin\Orchid\Screens\Delivery;

use Admin\Models\Delivery\Courier;
use Admin\Orchid\Screens\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class CourierListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,object>
     */
    public function query(): iterable
    {
        return [
            'datarows' => Courier::filters()->defaultSort('created_at', 'DESC')->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Список курьеров';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Администрирование курьеров и ссылок для чаевых';
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
            ModalToggle::make('Создать')
                ->modal('edit-model')
                ->title('Создание курьера')
                ->method('save')
                ->icon('bs.plus-circle')
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
                Input::make('model.name')
                    ->title('ФИО курьера')
                    ->type('text')
                    ->max(255)
                    ->required(),
                Input::make('model.phone')
                    ->title('Телефон курьера')
                    ->type('text')
                    ->max(16)
                    ->mask('+7 (999) 999-99-99')
                    ->required(),
                Input::make('model.cloudtips')
                    ->title('Ссылка CloudTips')
                    ->type('text')
                    ->max(255)
                    ->required(),
                CheckBox::make('model.active')
                    ->title('Активность')
                    ->sendTrueOrFalse(),
            ]))
                ->title('Редактирование')
                ->closeButton('Закрыть')
                ->applyButton('Сохранить')
                ->method('save')
                ->size(Modal::SIZE_LG)
                ->deferred('loadDataOnOpen'),

            // Таблица списка моделей
            Layout::table('datarows', [
                TD::make('name', 'ФИО курьера')
                    ->sort()
                    ->filter(Input::make()),
                TD::make('phone', 'Телефон курьера')
                    ->filter(Input::make()),
                TD::make('cloudtips', 'Ссылка CloudTips'),
                TD::make('created_at', 'Дата создания')
                    ->sort()
                    ->render(fn($model) => $model->created_at->format('d.m.Y H:i:s')),
                TD::make('Действия')
                    ->cantHide()
                    ->width('100px')
                    ->render(fn($model) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            ModalToggle::make('Редактировать')
                                ->modal('edit-model', [
                                    'model' => $model->id
                                ])
                                ->method('save')
                                ->icon('bs.pencil')
                                ->canSee(Auth::user()->hasAccess(Permission::DELIVERY_EDIT)),
                            Button::make('Удалить')
                                ->icon('bs.trash')
                                ->confirm('Действительно хотите удалить запись?')
                                ->method('remove', [
                                    'id' => $model->id,
                                ])
                                ->canSee(Auth::user()->hasAccess(Permission::DELIVERY_REMOVE))
                        ])),
            ]),
        ];
    }

    /**
     * Получить данные модели
     *
     * @return array<string,Courier>
     */
    public function loadDataOnOpen(Courier $model): iterable
    {
        return [
            'model' => $model
        ];
    }

    /**
     * Сохранить
     */
    public function save(Courier $model, Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'model.name' => ['required', 'string', 'max:255'],
            'model.phone' => ['required', 'string', 'max:18',
                Rule::unique(Courier::class, 'phone')->ignore($model->id)],
            'model.cloudtips' => ['required', 'string', 'active_url', 'max:255',
                Rule::unique(Courier::class, 'cloudtips')->ignore($model->id)],
            'model.active' => ['boolean'],
        ]);

        $model->fill($request->collect('model')->toArray());
        $model->save();

        Toast::info('Запись сохранена');
        return redirect()->route('delivery.courier.list');
    }

    public function remove(int $id): \Illuminate\Http\RedirectResponse
    {
        Courier::findOrFail($id)->delete();

        Toast::info('Запись удалена');
        return redirect()->route('delivery.courier.list');
    }
}
