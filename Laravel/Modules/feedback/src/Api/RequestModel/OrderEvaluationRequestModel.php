<?php

namespace Feedback\Api\RequestModel;

class OrderEvaluationRequestModel
{
    public function __construct(
        public string $order_id,
        public string $user_id,
        public int $evaluation,
        public ?string $comment,
        public ?array $comments,
        public ?array $images
    ) {
    }
}
