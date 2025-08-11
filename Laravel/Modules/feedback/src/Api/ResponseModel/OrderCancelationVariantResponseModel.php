<?php

namespace Feedback\Api\ResponseModel;

final class OrderCancelationVariantResponseModel
{
    public function __construct(
        public string $reason
    ) {
    }
}
