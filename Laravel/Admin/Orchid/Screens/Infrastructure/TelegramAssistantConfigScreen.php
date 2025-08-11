<?php

namespace Admin\Orchid\Screens\Infrastructure;

use Admin\Orchid\Screens\Permission;
use Api\TelegramAssistant\TelegramAssistantSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class TelegramAssistantConfigScreen extends Screen
{
    public function query(TelegramAssistantSettings $settings): array
    {
        return [
            'config' => $settings->toArray()
        ];
    }

    public function name(): string
    {
        return 'Настройки ТГ Ассистента';
    }

    public function commandBar(): array
    {
        return [
            Button::make('Сохранить')
                ->icon('check')
                ->method('save')
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('config.blizkoChatId')
                    ->title('Чат ID Blizko'),

                Input::make('config.blizkoEmergencyThreadId')
                    ->title('Thread ID Blizko ЧП'),

                Input::make('config.blizkoOrderEvaluateThreadId')
                    ->title('Thread ID Blizko Оценки заказов')
            ])
        ];
    }

    public function save(Request $request, TelegramAssistantSettings $settings): RedirectResponse
    {
        if (!Auth::user()->hasAccess(Permission::SYSTEM)) {
            Toast::info('Нет доступа');
            return redirect()->route('dashboard');
        }

        $settings->blizkoChatId = $request->input('config.blizkoChatId');
        $settings->blizkoEmergencyThreadId = $request->input('config.blizkoEmergencyThreadId');
        $settings->blizkoOrderEvaluateThreadId = $request->input('config.blizkoOrderEvaluateThreadId');

        $settings->save();

        Toast::info('Настройки успешно обновлены.');

        return redirect()->route('system.tg_assistant_config');
    }
}
