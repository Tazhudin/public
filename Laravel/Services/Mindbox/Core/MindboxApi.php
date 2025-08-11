<?php

namespace Api\Services\Mindbox\Core;

use Api\Infrastructure\User;
use Illuminate\Log\Logger;
use Mindbox\DTO\V3\OperationDTO;
use Mindbox\Exceptions\MindboxClientException;
use Mindbox\Mindbox;
use Mindbox\MindboxResponse;

class MindboxApi
{
    private const int SYNC_TIMEOUT = 5;
    private Logger $logger;

    public function __construct()
    {
        $this->logger = app()->make(Logger::class);
    }
    public function sendAnalytic(
        OperationDTO $operationDTO,
        string       $operationName,
        User         $user,
        bool         $isSync = false
    ): ?MindboxResponse {
        return $this->sendRequest($operationName, $operationDTO, $user, $isSync);
    }
    public function getPersonalRecommendationProducts(User $user, int $limit): ?MindboxResponse
    {
        $dto = new OperationDTO([
            'recommendation' => [
                'limit' => $limit,
            ],
            'customer' => [
                'ids' => [
                    'darkstoreID' => $user->getUserInfo()->id,
                ],
            ],
        ]);

        return $this->sendRequest('BlizkoEventPersonalRecommendations', $dto, $user, true);
    }

    public function getRelatedProducts(User $user, string $productId, int $limit): ?MindboxResponse
    {
        $dto = new OperationDTO([
            'recommendation' => [
                'limit' => $limit,
                'product' => [
                    'ids' => [
                        'darkstore' => $productId,
                    ],
                ],
            ],
        ]);

        return $this->sendRequest('BlizkoAssociatedProducts', $dto, $user, true);
    }

    public function getMayBeMoreProducts(User $user, int $limit): ?MindboxResponse
    {
        $dto = new OperationDTO([
            'recommendation' => [
                'limit' => $limit,
            ],
            'customer' => [
                'ids' => [
                    'darkstoreID' => $user->getUserInfo()->id,
                ],
            ],
        ]);

        return $this->sendRequest('DarkstorePersonalReccomendations', $dto, $user, true);
    }

    private function sendRequest(
        string       $operationName,
        OperationDTO $dto,
        User         $user,
        bool         $isSync = false
    ): ?MindboxResponse {
        try {
            return $this->mindbox($user->clientApp, $isSync)->getClientV3()
                ->prepareRequest(
                    'POST',
                    self::operationName($user->clientApp, $operationName),
                    $dto,
                    '',
                    ['deviceUUID' => $user->deviceUuid],
                    $isSync,
                    true
                )
                ->sendRequest();
        } catch (MindboxClientException $e) {
            $this->logger->error("Mindbox | Ошибка в операции $operationName: " . $e->getMessage());
            return null;
        }
    }

    private function mindbox(string $source, bool $isSync): Mindbox
    {
        return new Mindbox([
            'endpointId' => config("services.mindbox.$source.endpoint_id"),
            'secretKey' => config("services.mindbox.$source.secret_key"),
            'domainZone' => config('services.mindbox.domain_zone'),
            'domain' => 'mindbox.ru',
            'timeout' => $isSync ? self::SYNC_TIMEOUT : null,
        ], $this->logger);
    }

    private static function operationName(string $source, string $operationName): string
    {
        $noPrefix = [
            'SetKorzinaDarkstorItemList',
            'DarkstorePersonalReccomendations',
            'BlizkoEventPersonalRecommendations',
            'BlizkoAssociatedProducts',
        ];

        if (in_array($operationName, $noPrefix, true)) {
            return $operationName;
        }

        return match ($source) {
            'ios', 'android' => "Mobile.$operationName",
            default => "Website.$operationName",
        };
    }
}
