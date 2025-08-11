<?php

namespace Feedback\Api\ResponseModel;

final class OrderCancelationResponseModel
{
    public function __construct(
        public string $customer_id,
        public string $order_id,
        public string $order_status,
        public string $reason,
        public ?string $comment,
    ) {
    }
}
