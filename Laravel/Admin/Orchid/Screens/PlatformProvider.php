<?php

declare(strict_types=1);

namespace Admin\Orchid\Screens;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            Menu::make('Заказы')
                ->icon('bs.list')
                ->permission(Permission::SHOW_ORDER)
                ->route('order.list'),

            Menu::make('Каталог')
                ->icon('bs.collection')
                ->permission([Permission::CATALOG_READ])
                ->list([
                    Menu::make('Категории')
                        ->icon('bs.diagram-3')
                        ->route('catalog.category.root'),
                    Menu::make('Товары')
                        ->icon('bs.boxes')
                        ->route('catalog.product.list'),
                    Menu::make('Тэги')
                        ->icon('bs.tag')
                        ->route('catalog.tag.list'),
                    Menu::make('Новинки')
                        ->icon('bs.h-circle')
                        ->route('catalog.newproduct.list'),
                ]),

            Menu::make('Маркетинг')
                ->icon('bs.gift')
                ->permission([Permission::MARKETING_READ])
                ->list([
                    Menu::make('Подборки')
                        ->icon('bs.journals')
                        ->route('marketing.selection.list'),
                    Menu::make('Промокоды')
                        ->icon('bs.gift')
                        ->route('marketing.promocode.list'),
                    Menu::make('Сторисы')
                        ->icon('bs.book')
                        ->route('marketing.story.list'),
                    Menu::make('Группы сторисов')
                        ->icon('bs.journals')
                        ->route('marketing.storygroup.list'),
                    Menu::make('Группы для главной')
                        ->icon('bs.journals')
                        ->route('marketing.rootstorygroup.list'),
                ]),

            Menu::make('Обратная связь')
                ->icon('bs.collection')
                ->permission([Permission::FEEDBACK_READ])
                ->list([
                    Menu::make('Вопросы-ответы')
                        ->icon('bs.question')
                        ->route('feedback.faq.list'),
                    Menu::make('Ошибки банка')
                        ->icon('bs.translate')
                        ->route('feedback.paymentmessage.list'),
                    Menu::make('Варианты оценок')
                        ->icon('bs.star-half')
                        ->route('feedback.orderevaluationvariant.list'),
                    Menu::make('Варианты отмены')
                        ->icon('bs.star-half')
                        ->route('feedback.ordercancelationvariant.list'),

                    Menu::make('Оценки заказов')
                        ->icon('bs.star')
                        ->route('feedback.orderevaluation.list'),
                    Menu::make('Причины отмены')
                        ->icon('bs.star')
                        ->route('feedback.ordercancelation.list'),
                    Menu::make('ОС по ассортименту')
                        ->icon('bs.star')
                        ->route('feedback/wishes-products'),
                    Menu::make('Контакты')
                        ->icon('bs.people')
                        ->route('contacts')
                ]),
            Menu::make('Доставка')
                ->icon('bs.truck')
                ->permission([Permission::DELIVERY])
                ->list([
                    Menu::make('Склады')
                        ->icon('bs.boxes')
                        ->route('delivery.stock.list'),
                    Menu::make('Зоны доставки')
                        ->icon('bs.border')
                        ->route('delivery.area.list'),
                    Menu::make('Типы доставки')
                        ->icon('bs.car-front')
                        ->route('delivery.type.list'),
                    Menu::make('Курьеры')
                        ->icon('bs.bicycle')
                        ->route('delivery.courier.list'),
                    Menu::make('Вне зоны доставки')
                        ->icon('bs.compass')
                        ->route('delivery.outsideaddress.list')
                ]),
            Menu::make('Настройки')
                ->icon('bs.gear')
                ->permission([Permission::SYSTEM])
                ->list([
                    Menu::make('Фича флаги')
                        ->icon('bs.toggle-on')
                        ->permission([Permission::FEATURE_FLAGS])
                        ->route('feature.flags'),
                    Menu::make('Настройки ТГ Ассистента')
                        ->icon('bs.telegram')
                        ->route('system.tg_assistant_config'),
                    Menu::make('Файлы обмена')
                        ->icon('bs.journal-arrow-up')
                        ->route('system.exchange'),
                    Menu::make('PhpInfo')
                        ->icon('bs.info')
                        ->route('system.phpinfo'),
                ]),
            Menu::make('Покупатели')
                ->icon('bs.bell')
                ->permission([Permission::CUSTOMER])
                ->route('customer.list'),

            Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access Controls')),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider(),
        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users'))
                ->addPermission(Permission::FEATURE_FLAGS, 'Фича флаги')
                ->addPermission(Permission::SYSTEM, 'Окружение'),
            ItemPermission::group('Заказы')
                ->addPermission(Permission::SHOW_ORDER, 'Просмотр заказов'),
            ItemPermission::group('Магазин')
                ->addPermission(Permission::CATALOG_READ, 'Каталог')
                ->addPermission(Permission::CATALOG_EDIT, 'Каталог редактор')
                ->addPermission(Permission::MARKETING_READ, 'Маркетинг')
                ->addPermission(Permission::MARKETING_EDIT, 'Маркетинг редактор')
                ->addPermission(Permission::FEEDBACK_READ, 'Обратная связь')
                ->addPermission(Permission::FEEDBACK_EDIT, 'Обратная связь редактор')
                ->addPermission(Permission::DELIVERY, 'Доставка')
                ->addPermission(Permission::DELIVERY_EDIT, 'Доставка редактор')
                ->addPermission(Permission::DELIVERY_REMOVE, 'Доставка удаление')
                ->addPermission(Permission::DELIVERY_SKLAD, 'Управление складом')
                ->addPermission(Permission::CUSTOMER, 'Покупатель')
        ];
    }
}
