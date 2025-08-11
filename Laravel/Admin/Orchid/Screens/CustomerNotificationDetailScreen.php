<?php

namespace Admin\Orchid\Screens;

use Admin\Orchid\Components\Json;
use Admin\Orchid\Models\Customer\CustomerNotification;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Layout;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Support\Color;

class CustomerNotificationDetailScreen extends Screen
{
    /**
     * @return array<string, mixed>
     */
    public function query(CustomerNotification $notification): iterable
    {
        return [
            'notification' => $notification,
        ];
    }

    public function name(): ?string
    {
        return 'Уведомление';
    }


    /**
     * @return Layout[]
     * @throws \ReflectionException
     */
    public function layout(): iterable
    {
        return [
            \Orchid\Support\Facades\Layout::legend('notification', [
                Sight::make('customer', 'Клиент')->render(function (CustomerNotification $notification) {
                    return Link::make($notification->customer->getDisplayDescription())
                        ->type(Color::BASIC)
                        ->icon('bs.person-bounding-box')
                        ->route('customer.detail', $notification->customer->id);
                }),
                Sight::make('created_at', 'Дата')->asComponent(DateTimeSplit::class),
                Sight::make('message', 'Сообщение'),
                Sight::make('type')->render(fn(CustomerNotification $notification) => $notification->type->name),
                Sight::make('status')->render(fn(CustomerNotification $notification) => $notification->status->name),
                Sight::make('payload')->asComponent(Json::class),
                Sight::make('provider_response', 'Response')->asComponent(Json::class),
            ])
        ];
    }
}
