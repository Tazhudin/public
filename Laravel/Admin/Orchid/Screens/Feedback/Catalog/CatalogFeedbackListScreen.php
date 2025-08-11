<?php

namespace Admin\Orchid\Screens\Feedback\Catalog;

use Admin\Orchid\Screens\Permission;
use Feedback\Infrastructure\Models\WishesProducts;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class CatalogFeedbackListScreen extends Screen
{
    public function __construct()
    {
    }

    public function query(Request $request): iterable
    {
        return [
            'datarows' => WishesProducts::latest()->paginate(10),
        ];
    }

    public function name(): ?string
    {
        return 'Обратная связь о каталоге';
    }

    public function description(): ?string
    {
        return 'Список обратной связи, оставленной пользователями для улучшения ассортимента каталога.';
    }

    public function permission(): ?iterable
    {
        return [Permission::CATALOG_READ];
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('datarows', [
                TD::make('id', 'ID')
                    ->sort()
                    ->cantHide()
                    ->render(fn(WishesProducts $feedback) => $feedback->id),
                TD::make('comment', 'Комментарий пользователя')
                    ->cantHide()
                    ->render(fn(WishesProducts $feedback) => $feedback->comment),
                TD::make('source', 'Источник')
                    ->render(fn(WishesProducts $feedback) => $feedback->source),
                TD::make('created_at', 'Склад')
                    ->render(fn(WishesProducts $feedback) => $feedback->store),
                TD::make('phone_number', 'Номер телефона')
                    ->render(fn(WishesProducts $feedback) => $feedback->phone_number),
                TD::make('created_at', 'Дата создания')
                    ->render(fn(WishesProducts $feedback) => $feedback->created_at),
            ]),
        ];
    }

    public function remove(string $id): \Illuminate\Http\RedirectResponse
    {
        $feedback = WishesProducts::findOrFail($id);
        $feedback->delete();

        Alert::info('Обратная связь успешно удалена.');

        return redirect()->route('feedback/wishes-products');
    }
}
