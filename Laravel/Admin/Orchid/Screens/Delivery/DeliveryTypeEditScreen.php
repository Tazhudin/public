<?php

namespace Admin\Orchid\Screens\Delivery;

use Admin\Models\Delivery\DeliveryType;
use Admin\Orchid\Screens\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class DeliveryTypeEditScreen extends Screen
{
    public $model;

    public function query(DeliveryType $deliveryType): iterable
    {
        $deliveryType->price = $this->transformPriceToArray($deliveryType->price);

        return [
            'model' => $deliveryType,
        ];
    }

    public function name(): ?string
    {
        return 'Редактирование типа доставки';
    }

    public function description(): ?string
    {
        return 'Создание и редактирование типа доставки';
    }

    public function permission(): ?iterable
    {
        return [
            Permission::DELIVERY,
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Button::make(__('Save'))
                ->icon('bs.check-circle')
                ->method('save'),

            Button::make(__('Remove'))
                ->icon('bs.trash3')
                ->method('remove')
                ->canSee($this->model->exists),
        ];
    }

    public function layout(): iterable
    {
        return [
            \Admin\Orchid\Layouts\Delivery\DeliveryTypeEditLayout::class,
        ];
    }

    public function save(DeliveryType $deliveryType, Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'model.name' => ['required', 'string'],
            'model.is_active' => ['boolean'],
            'model.delivery_type' => ['required', 'string'],
            'model.deliveryArea' => ['required', 'string'],
            'model.price' => ['required', 'array', new \Admin\Orchid\Rules\Delivery\DeliveryPriceValidation()],
        ]);

        $model = $request->get('model');
        $model['price'] = $this->transformPriceToJson($model['price']);
        $deliveryType->fill($model);

        $exists = \Admin\Models\Delivery\DeliveryType::where('delivery_type', $model['delivery_type'])
            ->whereHas('deliveryArea', function ($query) use ($model) {
                $query->where('id', $model['deliveryArea']);
            })
            ->whereKeyNot($deliveryType->id)
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors([
                'model.deliveryArea' => "Выбранный тип для зоны доставки с ID: {$model['deliveryArea']} существует"
            ])->withInput();
        }

        $deliveryType->save();

        $deliveryType->deliveryArea()->sync([$model['deliveryArea']]);

        Toast::info('Запись сохранена');
        return redirect()->route('delivery.type.list');
    }

    public function remove(DeliveryType $deliveryType): \Illuminate\Http\RedirectResponse
    {
        $deliveryType->delete();

        Toast::info('Запись удалена');
        return redirect()->route('delivery.type.list');
    }

    private function transformPriceToArray($price)
    {
        $decodedPrice = json_decode($price, true);

        return !empty($decodedPrice) ? Arr::map($decodedPrice['gradation_price'], function ($price) {
            return [
                'order_price' => $price['to'],
                'delivery_price' => $price['price'],
            ];
        }) : [];
    }

    private function transformPriceToJson($priceArray)
    {
        $gradationPrice = Arr::map($priceArray, function ($price) {
            return [
                'to' => $price['order_price'],
                'price' => $price['delivery_price'],
            ];
        });

        return json_encode(['gradation_price' => $gradationPrice]);
    }
}
