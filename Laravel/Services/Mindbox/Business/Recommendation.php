<?php

namespace Api\Services\Mindbox\Business;

use Api\Infrastructure\User;
use Api\Services\Mindbox\Core\MindboxApi;

class Recommendation
{
    private const int MAX_RECOM = 30;

    public function __construct(
        private readonly MindboxApi $mindboxApi,
    ) {
    }

    public function getPersonalRecommendationProductIds(User $user): array
    {
        $response = $this->mindboxApi->getPersonalRecommendationProducts($user, self::MAX_RECOM);

        return $this->extractProductIds($response);
    }

    public function getRelatedProductIds(User $user, string $productId): array
    {
        $response = $this->mindboxApi->getRelatedProducts($user, $productId, self::MAX_RECOM);

        return $this->extractProductIds($response);
    }

    public function getMayBeMoreProductIds(User $user): array
    {
        $response = $this->mindboxApi->getMayBeMoreProducts($user, self::MAX_RECOM);

        return $this->extractProductIds($response);
    }

    private function extractProductIds(?\Mindbox\MindboxResponse $response): array
    {
        if (!$response) {
            return [];
        }

        $result = $response->getBody();

        if (isset($result['status']) && $result['status'] === 'Success' && !empty($result['recommendations'])) {
            return array_map(fn($item) => $item['ids']['darkstore'], $result['recommendations']);
        }

        return [];
    }
}
