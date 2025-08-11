<?php

namespace Admin\Orchid\Screens\FeatureFlags;

use Admin\Models\FeatureFlags;
use Admin\Orchid\Components\SuccessOrFailure;
use Admin\Orchid\Screens\Permission;
use Laravel\Pennant\Feature;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Repository;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class FeatureFlagsListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     * @return array<string, object>
     */
    public function query(): iterable
    {
        return [
            'datarows' => FeatureFlags::asRepositories()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Фича флаги';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Администрирование фича-флагов';
    }

    /**
     * The permissions required to access this screen.
     *
     * @return iterable|null
     */
    public function permission(): ?iterable
    {
        return [
            Permission::FEATURE_FLAGS,
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('datarows', [
            TD::make('name', 'Название'),

            TD::make('state', 'Состояние')
                ->render(function (Repository $item) {
                    return (new SuccessOrFailure($item->get('state') ? 'Активен' : 'Неактивен', $item->get('state')))
                        ->render();
                }),

            TD::make('actions', 'Действия')
                ->render(function ($item) {
                    return Button::make($item->get('state') ? 'Деактивировать' : 'Активировать')
                        ->method('toggleActivation', ['name' => $item->get('name') ?? '']);
                }),
            ])
        ];
    }

    public function toggleActivation(string $name): \Illuminate\Http\RedirectResponse
    {
        if ($name == '') {
            Toast::info('Параметр name отсутствует');
            return redirect()->route('feature.flags');
        }

        $state = FeatureFlags::isActive($name);
        $stateToSet = !$state;

        $savedFeatureFlag = FeatureFlags::updateOrCreate(
            ['name' => $name],
            ['state' => $stateToSet]
        );

        if ($stateToSet) {
            Feature::activateForEveryone($savedFeatureFlag->name);
        } else {
            Feature::deactivateForEveryone($savedFeatureFlag->name);
        }

        Toast::info($stateToSet ? 'Фича флаг активирован' : 'Фича флаг деактивирован');

        return redirect()->route('feature.flags');
    }
}
