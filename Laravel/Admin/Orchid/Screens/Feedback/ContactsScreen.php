<?php

namespace Admin\Orchid\Screens\Feedback;

use Admin\Orchid\Screens\Permission;
use Api\Settings\Contacts as ContactsSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ContactsScreen extends Screen
{
    public $contacts;

    public function query(): array
    {
        return [
            'contacts' => app(ContactsSettings::class)->toArray()
        ];
    }

    public function name(): string
    {
        return 'Контакты';
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
                Input::make('contacts.phone')
                    ->title('Телефон')
                    ->type('text')
                    ->max(20)
                    ->required()
                    ->value($this->contacts['phone']),

                Input::make('contacts.email')
                    ->title('Почта')
                    ->type('email')
                    ->required()
                    ->value($this->contacts['email']),

                Input::make('contacts.telegram_link')
                    ->title('Телеграм')
                    ->type('text')
                    ->required()
                    ->value($this->contacts['telegram_link']),

                Input::make('contacts.whatsapp_link')
                    ->title('Ватсап')
                    ->type('text')
                    ->required()
                    ->value($this->contacts['whatsapp_link']),
            ])
        ];
    }

    public function save(Request $request)
    {
        if (!Auth::user()->hasAccess(Permission::FEEDBACK_EDIT)) {
            Toast::info('Нет доступа');
            return redirect()->route('contacts');
        }


        $request->validate([
            'contacts.phone' => ['required', 'string', 'max:250'],
            'contacts.email' => ['required', 'string', 'max:250'],
            'contacts.telegram_link' => ['nullable', 'string', 'max:250'],
            'contacts.whatsapp_link' => ['nullable', 'string', 'max:250'],
        ]);


        $contacts = app(ContactsSettings::class);

        $contacts->fill([
            'phone' => $request->input('contacts.phone'),
            'email' => $request->input('contacts.email'),
            'telegram_link' => $request->input('contacts.telegram_link'),
            'whatsapp_link' => $request->input('contacts.whatsapp_link'),
        ]);

        $contacts->save();

        Toast::info('Контакты успешно обновлены.');

        return redirect()->route('contacts');
    }
}
