<?php

namespace Api\Services\Mindbox\Business;

use Api\Events\CreateOrderEvent;
use Api\Events\SetBasketEvent;
use Api\Events\SetFavoriteEvent;
use Api\Events\ViewCategoryEvent;
use Api\Events\ViewProductEvent;
use Api\Infrastructure\User;
use Api\Services\Mindbox\Core\MindboxApi;
use Favorite\Api\FavoriteApi;
use Illuminate\Log\Logger;
use Mindbox\DTO\V3\OperationDTO;
use Mindbox\MindboxResponse;
use Order\Api\ResponseModel\OrderItemResponseModel;

class AnalyticEvent
{
    private MindboxApi $mindboxApi;
    private mixed $logger;

    public function __construct(MindboxApi $mindboxApi)
    {
        $this->logger = app()->make(Logger::class);
        $this->mindboxApi = $mindboxApi;
    }

    public function send(
        OperationDTO $operationDTO,
        string       $operationName,
        User         $user,
        bool         $isSync = false
    ): ?MindboxResponse {
        if (!$this->canSend($user)) {
            $this->logger->info('Mindbox | инфо: запрос не отправлен для пользователя: ' . $user->id);
            return null;
        }

        return $this->mindboxApi->sendAnalytic($operationDTO, $operationName, $user, $isSync);
    }

    public function viewProduct(ViewProductEvent $event): void
    {
        $operation = new OperationDTO([
            'viewProduct' => ['product' => ['ids' => ['darkstore' => $event->productId]]]
        ]);
        $this->send($operation, 'ViewProduct', $event->user);
    }

    public function viewCategory(ViewCategoryEvent $event): void
    {
        $operation = new OperationDTO([
            'viewProductCategory' => ['productCategory' => ['ids' => ['darkstore' => $event->categoryId]]]
        ]);
        $this->send($operation, 'ViewProductCategory', $event->user);
    }

    public function setFavorite(SetFavoriteEvent $event): void
    {
        $favorite = app()->make(FavoriteApi::class)->getByUserId($event->customerId);
        $operation = new OperationDTO([
            'productList' => array_map(fn($productId) => [
                'product' => ['ids' => ['darkstore' => $productId]],
                'count' => 1
            ], $favorite->productsIds)
        ]);
        $this->send($operation, 'SetWishList', $event->user);
    }

    public function setBasket(SetBasketEvent $event): void
    {
        $operation = new OperationDTO([
            'productList' => array_map(fn($item) => [
                'product' => ['ids' => ['darkstore' => $item->productId]],
                'count' => $item->quantity,
                'pricePerItem' => $item->price?->basePrice ?? 0,
            ], $event->basket)
        ]);
        $this->send($operation, 'SetKorzinaDarkstorItemList', $event->user);
    }

    public function createOrder(CreateOrderEvent $event): void
    {
        $operation = new OperationDTO([
            'customer' => ['ids' => ['darkstoreID' => $event->order->customerId]],
            'order' => [
                'ids' => ['darkstoreID' => $event->order->number],
                'deliveryCost' => $event->order->deliveryPrice,
                'totalPrice' => $event->order->totalAmount,
                'lines' => array_map(fn(OrderItemResponseModel $item, int $i) => [
                    'lineNumber' => $i,
                    'quantity' => $item->quantity,
                    'basePricePerItem' => $item->price->basePrice,
                    'discountedPricePerLine' => $item->price->getCurrentPrice(),
                    'product' => ['ids' => ['darkstore' => $item->productId]],
                ], $event->order->items, range(1, count($event->order->items)))
            ],
        ]);
        $this->send($operation, 'CreateAuthorizedOrder', $event->user);
    }

    private function canSend(User $user): bool
    {
        return $user->deviceUuid && config()->has("services.mindbox.{$user->clientApp}");
    }
}
