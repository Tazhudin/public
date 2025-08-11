<?php

namespace Feedback\Api\RequestModel;

class OrderCancelationRequestModel
{
    /**
     * @param array<string> $reason
     */
    public function __construct(
        public string $customer_id,
        public string $order_id,
        public string $order_status,
        public array $reason,
        public ?string $comment,
    ) {
    }
}
