<?php

namespace Feedback\Infrastructure;

use Feedback\Infrastructure\Models\OrderEvaluation;

class OrderEvaluateEvent
{
    public function __construct(
        public OrderEvaluation $evaluation,
    ) {
    }
}
