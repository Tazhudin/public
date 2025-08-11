<?php

namespace Admin\Orchid\Screens\Marketing;

use Admin\Models\Marketing\Promocode\Promocode;
use Admin\Models\Marketing\Promocode\PromocodeParam;
use Admin\Models\Marketing\Promocode\PromocodeParamDescriptionProvider;
use Admin\Models\Marketing\Promocode\RequestPromocodeParam;
use Admin\Orchid\Layouts\Marketing\PromocodeConstraintForm;
use Admin\Orchid\Layouts\RawCard;
use Admin\Orchid\Screens\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class PromocodeEditScreen extends Screen
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
    public function query(Promocode $promocode): iterable
    {
        return [
            'model' => $promocode,
            'effectDescription' => $this->descriptionProvider->description($promocode->effect),
            'conditions' => $promocode->conditions,
            'constraints' => $promocode->constraints,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Редактирование промокода';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Редактирование промокода';
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
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Save'))
                ->icon('bs.check-circle')
                ->method('save'),

            Button::make(__('Remove'))
                ->icon('bs.trash3')
                ->confirm('Действительно хотите удалить промокод?')
                ->method('remove'),
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
            Layout::modal('effect', [
                new \Admin\Orchid\Layouts\Marketing\PromocodeEffectForm()
            ])->applyButton('Сохранить')->title('Настроить действие'),
            Layout::modal('condition', [
                new \Admin\Orchid\Layouts\Marketing\PromocodeConditionForm()
            ])->method('setupCondition')
                ->applyButton('Сохранить')
                ->title('Настроить условие'),
            Layout::modal('constraint', [
                new PromocodeConstraintForm()
            ])->method('setupConstraint')
                ->applyButton('Сохранить')
                ->title('Настроить ограничение'),
            Layout::rows(
                [
                    Input::make('model.promocode')
                        ->title('Промокод')
                        ->type('text')
                        ->max(100)->readonly()
                        ->horizontal()
                        ->required(),
                    TextArea::make('model.description')
                        ->horizontal()
                        ->title('Описание'),
                    CheckBox::make('model.is_active')
                        ->title('Активность')
                        ->horizontal()
                        ->sendTrueOrFalse(),
                    Group::make([
                        DateTimer::make('model.active_from')
                            ->title('Начало активности')
                            ->format('d.m.Y')
                            ->serverFormat()
                            ->allowEmpty(),
                        DateTimer::make('model.active_to')
                            ->title('Окончание активности')
                            ->format('d.m.Y')
                            ->serverFormat()
                            ->allowEmpty()
                    ]),
                ]
            ),
            Layout::block(
                new RawCard('effectDescription'),
            )->title('Действие промокода')->commands([
                ModalToggle::make('Изменить')
                    ->modal('effect')
                    ->method('setupEffect'),
            ]),
            Layout::block([
                Layout::table('conditions', [
                    TD::make('description', 'Условие')->render(
                        fn(PromocodeParam $param) => $this->descriptionProvider->description($param)
                    ),
                    TD::make(title: 'Действия')
                        ->render(
                            fn(PromocodeParam $param) => Button::make('Delete')
                                ->icon('bs.trash')
                                ->method('removeCondition', ['conditionHash' => $param->hash()])
                                ->confirm('Хотите удалить условие?')
                        )->alignRight()
                ]),
            ])->commands([
                ModalToggle::make('Настроить')
                    ->modal('condition')
                    ->method('setupCondition'),
            ])->vertical()->title('Условия'),

            Layout::block([
                Layout::table('constraints', [
                    TD::make('description', 'Ограничение')->render(
                        fn(PromocodeParam $param) => $this->descriptionProvider->description($param)
                    ),
                    TD::make(title: 'Действия')
                        ->render(
                            fn(PromocodeParam $param) => Button::make('Delete')
                                ->icon('bs.trash')
                                ->method('removeConstraint', ['constraintHash' => $param->hash()])
                                ->confirm('Хотите удалить ограничение?')
                        )->alignRight()
                ]),
            ])->commands([
                ModalToggle::make('Настроить')
                    ->modal('constraint')
                    ->method('setupConstraint'),
            ])->vertical()->title('Ограничения')
        ];
    }

    public function save(Promocode $promocode, Request $request): RedirectResponse
    {
        $requestData = $request->validate([
            'model.description' => ['string', 'nullable'],
            'model.is_active' => ['boolean'],
            'model.active_from' => ['date', 'nullable'],
            'model.active_to' => ['date', 'nullable'],
        ]);

        $promocode->description = $requestData['model']['description'] ?? '';
        $promocode->is_active = $requestData['model']['is_active'];
        $promocode->active_from = $requestData['model']['active_from'];
        $promocode->active_to = $requestData['model']['active_to'];
        $promocode->save();

        Toast::info('Запись сохранена');

        return redirect()->route('marketing.promocode.edit', ['promocode' => $promocode->id]);
    }

    public function remove(Promocode $promocode): RedirectResponse
    {
        $promocode->delete();

        Toast::info('Запись удалена');

        return redirect()->route('marketing.promocode.list');
    }

    public function setupEffect(Promocode $promocode, Request $request): RedirectResponse
    {
        $promocode->effect = $this->requestPromocodeParam->getValidatedEffectFromRequest($request);
        $promocode->save();

        return redirect()->route('marketing.promocode.edit', ['promocode' => $promocode->id]);
    }

    public function setupCondition(Promocode $promocode, Request $request): RedirectResponse
    {
        $requestCondition = $this->requestPromocodeParam->getValidatedConditionFromRequest($request);

        /** @var Collection<int, PromocodeParam> $promocodeConditions */
        $promocodeConditions = $promocode->conditions;

        $index = $promocodeConditions
            ->search(fn(PromocodeParam $c) => $c->hash() === $requestCondition->hash());

        if ($index !== false) {
            $promocodeConditions->put($index, $requestCondition);
        } else {
            $promocodeConditions->add($requestCondition);
        }

        $promocode->conditions = $promocodeConditions;
        $promocode->save();

        return redirect()->route('marketing.promocode.edit', ['promocode' => $promocode->id]);
    }

    public function removeCondition(Promocode $promocode, string $conditionHash): RedirectResponse
    {
        /** @var Collection<int, PromocodeParam> $promocodeConditions */
        $promocodeConditions = $promocode->conditions;
        $index = $promocodeConditions->search(fn(PromocodeParam $c) => $c->hash() === $conditionHash);

        if ($index === false) {
            return redirect()->route('marketing.promocode.edit', ['promocode' => $promocode->id]);
        }

        $promocodeConditions->forget($index);
        $promocode->conditions = $promocodeConditions;
        $promocode->save();

        Toast::info('Условие удалено');

        return redirect()->route('marketing.promocode.edit', ['promocode' => $promocode->id]);
    }

    public function setupConstraint(Promocode $promocode, Request $request): RedirectResponse
    {
        $requestConstraint = $this->requestPromocodeParam->getValidatedConstraintsFromRequest($request);

        /** @var Collection<int, PromocodeParam> $promocodeConstraints */
        $promocodeConstraints = $promocode->constraints;

        $index = $promocodeConstraints->search(fn(PromocodeParam $c) => $c->hash() === $requestConstraint->hash());

        if ($index !== false) {
            $promocodeConstraints->put($index, $requestConstraint);
        } else {
            $promocodeConstraints->add($requestConstraint);
        }

        $promocode->constraints = $promocodeConstraints;
        $promocode->save();

        return redirect()->route('marketing.promocode.edit', ['promocode' => $promocode->id]);
    }

    public function removeConstraint(Promocode $promocode, string $constraintHash): RedirectResponse
    {
        /** @var Collection<int, \Admin\Models\Marketing\Promocode\PromocodeParam> $promocodeConstraints */
        $promocodeConstraints = $promocode->constraints;
        $index = $promocodeConstraints->search(fn(PromocodeParam $c) => $c->hash() === $constraintHash);

        if ($index === false) {
            return redirect()->route('marketing.promocode.edit', ['promocode' => $promocode->id]);
        }

        $promocodeConstraints->forget($index);
        $promocode->constraints = $promocodeConstraints;
        $promocode->save();

        Toast::info('Ограничение удалено');

        return redirect()->route('marketing.promocode.edit', ['promocode' => $promocode->id]);
    }
}
