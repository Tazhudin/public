<?php

namespace Admin\Orchid\Screens\Marketing;

use Admin\Models\Marketing\Promocode\ExistsPromocodeRule;
use Admin\Models\Marketing\Promocode\Promocode;
use Admin\Models\Marketing\Promocode\PromocodeParam;
use Admin\Models\Marketing\Promocode\PromocodeParamDescriptionProvider;
use Admin\Models\Marketing\Promocode\RequestPromocodeParam;
use Admin\Orchid\Components\Html;
use Admin\Orchid\Layouts\Marketing\PromocodeEffectForm;
use Admin\Orchid\Screens\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Repository;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class PromocodeListScreen extends Screen
{
    public function __construct(
        private readonly PromocodeParamDescriptionProvider $descriptionProvider,
        private readonly RequestPromocodeParam $requestPromocodeParam
    ) {
    }

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array<string,object>
     */
    public function query(): iterable
    {
        return [
            'datarows' => Promocode::filters()->defaultSort('created_at', 'DESC')->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Список Промокодов';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Администрирование промокодов';
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
            ModalToggle::make(__('Add'))
                ->modal('create-promocode')
                ->icon('bs.plus-circle')
                ->method('createPromocode')
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
        echo '';
        return [
            Layout::modal('create-promocode', [
                Layout::rows([
                    Input::make('promocode')
                        ->title('Промокод')
                        ->type('text')
                        ->max(100)
                        ->required(),
                    TextArea::make('description')
                        ->title('Описание'),
                    CheckBox::make('is_active')
                        ->title('Активность')
                        ->sendTrueOrFalse()
                        ->checked(),
                ]),
                new PromocodeEffectForm()
            ])->method('createPromocode')
                ->applyButton('Добавить')
                ->title('Добавить промокод'),
            $this->promocodeModalDetail(),
            // Таблица списка моделей
            Layout::table('datarows', [
                TD::make('promocode', 'Промокод')
                    ->sort()
                    ->render(fn(Promocode $model) => ModalToggle::make($model->promocode)
                        ->type(Color::BASIC)
                        ->modal('promocodeDetail', [
                            'promocode' => $model->id
                        ]))
                    ->cantHide()
                    ->filter(),
                TD::make('description', 'Описание'),
                TD::make('is_active', 'Активность')
                    ->sort()
                    ->render(fn($model) => CheckBox::make()
                        ->disabled(true)
                        ->checked($model->is_active)),
                TD::make('active_date', 'Даты активности')
                    ->render(function ($model) {
                        return
                            (optional(
                                $model->active_from,
                                fn($date) => 'С ' . $date->format('d.m.Y') . '<br/>'
                            ) ?? '') .
                            (optional(
                                $model->active_to,
                                fn($date) => 'До ' . $date->format('d.m.Y')
                            ) ?? '');
                    }),
                TD::make('Действия')
                    ->cantHide()
                    ->width('100px')
                    ->render(fn($model) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make('Редактировать')
                                ->icon('bs.pencil')
                                ->route('marketing.promocode.edit', $model->id)
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
     * @return array<string, mixed>
     */
    public function promocodeDetail(Promocode $promocode): array
    {
        return [
            'data' => new Repository([
                'promocode' => $promocode->promocode,
                'description' => $this->descriptionProvider->description($promocode->effect),
                'is_active' => $promocode->is_active,
                'active_from' => $promocode->active_from,
                'active_to' => $promocode->active_to,
                'effect' => $this->descriptionProvider->description($promocode->effect),
                'conditions' => $promocode->conditions->map(
                    fn(PromocodeParam $param) => $this->descriptionProvider->description($param)
                )->join('<hr class="border-secondary"/>'),
                'constraints' => $promocode->constraints->map(
                    fn(PromocodeParam $param) => $this->descriptionProvider->description($param)
                )->join('<hr class="border-secondary"/>')
            ])
        ];
    }

    public function createPromocode(Request $request): ?RedirectResponse
    {
        $requestData = $request->validate([
            'promocode' => ['required', 'string', 'max:100', new ExistsPromocodeRule()]
        ]);

        $promocode = new Promocode();
        $promocode->promocode = $requestData['promocode'];
        $promocode->effect = $this->requestPromocodeParam->getValidatedEffectFromRequest($request);

        $promocode->save();

        Toast::info('Промокод создан');

        return redirect()->route('marketing.promocode.edit', ['promocode' => $promocode->id]);
    }

    public function remove(int $id): \Illuminate\Http\RedirectResponse
    {
        Promocode::findOrFail($id)->delete();

        Toast::info('Запись удалена');
        return redirect()->route('marketing.promocode.list');
    }

    /**
     * @throws \ReflectionException
     */
    private function promocodeModalDetail(): Modal
    {
        return Layout::modal('promocodeDetail', [
            Layout::legend('data', [
                Sight::make('effect', 'Действие')->usingComponent(Html::class),
                Sight::make('conditions', 'Условия')->usingComponent(Html::class),
                Sight::make('constraints', 'Ограничения')->usingComponent(Html::class),
            ])
        ])->title('Детали промокода')
            ->closeButton('Закрыть')
            ->withoutApplyButton()
            ->size(Modal::SIZE_XL)
            ->deferred('promocodeDetail')
            ->rawClick();
    }
}
